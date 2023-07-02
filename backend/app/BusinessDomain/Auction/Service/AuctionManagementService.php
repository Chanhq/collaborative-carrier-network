<?php

namespace App\BusinessDomain\Auction\Service;

use App\BusinessDomain\Auction\Exception\OngoingAuctionFoundException;
use App\BusinessDomain\RevenueCalculation\Service\TransportCostCalculationService;
use App\BusinessDomain\RevenueCalculation\Service\TransportPriceCalculationService;
use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Models\Auction;
use App\Models\AuctionEvaluation;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\AuctionBid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuctionManagementService
{
    private const REVENUE_THRESHOLD = 70;

    public function __construct(
        private readonly TransportCostCalculationService $costCalculationService,
        private readonly TransportPriceCalculationService $priceCalculationService,
        private readonly PythonVehicleRoutingWrapper $vehicleRoutingWrapper,
    ) {
    }

    /**
     * @throws Throwable|OngoingAuctionFoundException
     */
    public function startAuction(): void
    {
        DB::beginTransaction();
        try {
            $eligibleTransportRequests = $this->getTransportRequestEligibleForAuction();

            $startedAuction = new Auction();
            $startedAuction->save();

            foreach ($eligibleTransportRequests as $transportRequest) {
                $transportRequest->status = TransportRequestStatusEnum::Selected;
                $transportRequest->auction()->associate($startedAuction);
                $transportRequest->save();
                $this->submitBids($transportRequest);
            }

            $this->evaluateBids($eligibleTransportRequests);

            $startedAuction->save();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();
    }

    /**
     * @return array<TransportRequest>
     */
    private function getTransportRequestEligibleForAuction(): array
    {
        $eligibleTransportRequests = [];

        $pristineTransportRequests =
            TransportRequest::all()->where('status', '=', TransportRequestStatusEnum::Pristine);

        /** @var TransportRequest $candidateTransportRequest */
        foreach ($pristineTransportRequests as $candidateTransportRequest) {
            $candidateRevenue = $this->calculateRevenue($candidateTransportRequest);

            if ($candidateRevenue < self::REVENUE_THRESHOLD) {
                $eligibleTransportRequests[] = $candidateTransportRequest;
            }
        }
        return $eligibleTransportRequests;
    }


    /**
     * @return TransportRequest[]
     */
    private function convertTransportRequests(HasMany $transportRequests): array
    {
        $convertedTransportRequests = [];
        /** @var TransportRequest $transportRequest */
        foreach ($transportRequests->get() as $transportRequest) {
            $convertedTransportRequests[] = $transportRequest;
        }

        return $convertedTransportRequests;
    }

    /**
     * Calculate the revenue for the optimal path
     *
     * @param TransportRequest $candidateTransportRequest
     * @return float
     */
    private function calculateRevenue(TransportRequest $candidateTransportRequest): float
    {
        /** @var User $transportRequestIssuer */
        $transportRequestIssuer = $candidateTransportRequest->user()->first();

        $usersTransportRequests = $this->convertTransportRequests($transportRequestIssuer->transportRequests());
        $usersTransportRequestsWithoutCandiate = $this->convertTransportRequests(
            $candidateTransportRequest->user()->first()
                ->transportRequests()->where('id', '!=', $candidateTransportRequest->id)
        );

        $optimalPathWithCandidate =
            $this->vehicleRoutingWrapper->findOptimalPath($usersTransportRequests);
         $optimalPathWithoutCandidate =
            $this->vehicleRoutingWrapper->findOptimalPath($usersTransportRequestsWithoutCandiate);

        return $this->priceCalculationService->calculatePriceForTransportRequest(
            $candidateTransportRequest,
            $transportRequestIssuer
        )
        - $this->costCalculationService->calculateTransportRequestCost(
            $optimalPathWithCandidate,
            $optimalPathWithoutCandidate,
            $transportRequestIssuer,
        );
    }

    /**
     * Calculate and submit bids for carriers
     *
     * @param TransportRequest $transportRequest
     */
    private function submitBids(TransportRequest $transportRequest): void
    {
        $eligibleUsers = $this->getEligibleUsers();
        /** @var User $transportRequestIssuer */
        $transportRequestIssuer = $transportRequest->user()->first();

        foreach ($eligibleUsers as $user) {
            if ($user->id() === $transportRequestIssuer->id()) {
                continue;
            }

            $bidAmount = $this->calculateBidAmount() + (float)random_int(1, 10);

            $this->storeAuctionBid($transportRequest, $user, $bidAmount);
        }
    }

    /**
     * Get the eligible carriers for the transport request
     *
     * @return array<User>
     */
    private function getEligibleUsers(): array
    {
        return User::all()->where('is_auctioneer', false)->all();
    }

    /**
     * Calculate the worth of the transport request for the carrier
     *
     * @return float
     */
    private function calculateBidAmount(): float
    {
        $bidAmounts = [];

        $pristineTransportRequests =
            TransportRequest::all()->where('status', '=', TransportRequestStatusEnum::Pristine);

        /** @var TransportRequest $candidateTransportRequest */
        foreach ($pristineTransportRequests as $candidateTransportRequest) {
            $candidateRevenue = $this->calculateRevenue($candidateTransportRequest);

            $bidAmounts[] = $candidateRevenue * 0.8;
        }

        return array_sum($bidAmounts);
    }

    /**
     * Submit the bid for the transport request from the carrier
     */
    private function storeAuctionBid(TransportRequest $transportRequest, User $user, float $bidAmount): void
    {
        /** @var Auction $auction */
        $auction = $transportRequest->auction()->first();

        if (!$auction) {
            throw new \InvalidArgumentException('Transport request does not belong to any auction.');
        }

        Log::notice('Bid', [
            'auction_id' => $auction->id(),
            'user_id' => $user->id(),
            'transport_request_id' => $transportRequest->id(),
            'bid_amount' => $bidAmount,
        ] );


        // Create or update the bid for the carrier in the auction
        $bid = new AuctionBid([
            'auction_id' => $auction->id(),
            'user_id' => $user->id(),
            'transport_request_id' => $transportRequest->id(),
            'bid_amount' => $bidAmount,
        ]);
        $bid->save();
    }

    /**
     * @param array<TransportRequest> $auctionedTransportRequests
     */
    private function evaluateBids(array $auctionedTransportRequests): void
    {
        foreach ($auctionedTransportRequests as $transportRequest) {
            $bids = $transportRequest->bids()->orderBy('bid_amount', 'desc')->get()->all();

            if (empty($bids)) {
                $transportRequest->markAsUnsold();
                continue;
            }

            if (count($bids) > 1) {
                $winningBid = $bids[0];
                $priceDefiningBid = $bids[1];
            } else {
                $winningBid = $bids[0];
                $priceDefiningBid = $bids[0];
            }


            /** @var User $winningCarrier */
            $winningCarrier = User::find($winningBid['user_id']);
            $transportRequest->user()->associate($winningCarrier);
            $transportRequest->markAsCompleted();
            $transportRequest->save();

            Log::notice('Bids', $bids);
            $bidEvaluation = new AuctionEvaluation([
                'auction_id' => $winningBid['auction_id'],
                'user_id' => $winningCarrier->id(),
                'transport_request_id' => $transportRequest->id(),
            ]);

            $bidEvaluation->save();
        }

    }
}
