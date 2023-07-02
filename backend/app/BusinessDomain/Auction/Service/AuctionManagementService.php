<?php

namespace App\BusinessDomain\Auction\Service;

use App\BusinessDomain\Auction\Exception\OngoingAuctionFoundException;
use App\BusinessDomain\RevenueCalculation\Service\TransportCostCalculationService;
use App\BusinessDomain\RevenueCalculation\Service\TransportPriceCalculationService;
use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Infrastructure\Eloquent\HasManyRelationShipToArrayConverter;
use App\Models\Auction;
use App\Models\AuctionEvaluation;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\AuctionBid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class AuctionManagementService
{
    private const REVENUE_THRESHOLD = 70;

    public function __construct(
        private readonly TransportCostCalculationService $costCalculationService,
        private readonly TransportPriceCalculationService $priceCalculationService,
        private readonly PythonVehicleRoutingWrapper $vehicleRoutingWrapper,
        private readonly HasManyRelationShipToArrayConverter $toArrayConverter,
    ) {
    }

    /**
     * @throws Throwable|OngoingAuctionFoundException
     * @return array<int, float> maps user_id to the price he has to pay for bought transport requests in the auction
     */
    public function auctionTransportRequests(): array
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

            $auctionPriceUserMap = $this->evaluateBids($eligibleTransportRequests);
            $startedAuction->save();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        return $auctionPriceUserMap;
    }

    /**
     * @param array<int, float>  $auctionPriceUserMap
     * maps user_id to the price he has to pay for bought transport requests in the auction
     */
    public function evaluateAuction(array $auctionPriceUserMap): void
    {
        /** @var Collection<Auction> $activeAuctionCollection */
        $activeAuctionCollection = Auction::active()->get();
        /** @var Auction $currentlyOngoingAuction */
        $currentlyOngoingAuction = $activeAuctionCollection->first();

        foreach ($auctionPriceUserMap as $userId => $priceToPay) {
            /** @var User $user */
            $user = User::find($userId);
            /** @var array<TransportRequest> $transportRequestArray */
            $transportRequestArray = $this->toArrayConverter->convert($user->transportRequests());

            $revenueGain = $this->priceCalculationService->calculatePriceForTransportRequestSet(
                $transportRequestArray,
                $user
            )
                - $user->transportRequestSetRevenuePreAuction();

            $bidEvaluation = new AuctionEvaluation([
                'auction_id' => $currentlyOngoingAuction->id(),
                'user_id' => $userId,
                'price_to_pay' => $priceToPay,
                'revenue_gain' => $revenueGain,
            ]);

            $bidEvaluation->save();
        }
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
     * Calculate the revenue for the optimal path
     *
     * @param TransportRequest $candidateTransportRequest
     * @return float
     * @throws \JsonException
     */
    private function calculateRevenue(TransportRequest $candidateTransportRequest): float
    {
        /** @var User $transportRequestIssuer */
        $transportRequestIssuer = $candidateTransportRequest->user()->first();

        /** @var array<TransportRequest> $usersTransportRequests */
        $usersTransportRequests = $this->toArrayConverter->convert($transportRequestIssuer->transportRequests());
        /** @var array<TransportRequest> $usersTransportRequestsWithoutCandidate */
        $usersTransportRequestsWithoutCandidate = $this->toArrayConverter->convert(
            $candidateTransportRequest->user()->first()
                ->transportRequests()->where('id', '!=', $candidateTransportRequest->id)
        );

        $optimalPathWithCandidate =
            $this->vehicleRoutingWrapper->findOptimalPath($usersTransportRequests);
         $optimalPathWithoutCandidate =
            $this->vehicleRoutingWrapper->findOptimalPath($usersTransportRequestsWithoutCandidate);

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
        $eligibleUsers = User::carriers();
        /** @var User $transportRequestIssuer */
        $transportRequestIssuer = $transportRequest->user()->first();

        foreach ($eligibleUsers as $user) {
            if ($user->id() === $transportRequestIssuer->id()) {
                continue;
            }

            $pristineTransportRequests =
                TransportRequest::all()->where('status', '=', TransportRequestStatusEnum::Selected);

            /** @var TransportRequest $candidateTransportRequest */
            foreach ($pristineTransportRequests as $candidateTransportRequest) {
                $bidAmount = 0;

                $bidAmount = $this->calculateRevenue($candidateTransportRequest) * 0.8;

                if ($bidAmount <= 0) {
                    continue;
                }

                $this->storeAuctionBid($transportRequest, $user, $bidAmount);
            }
        }
    }

    /**
     * Submit the bid for the transport request from the carrier
     */
    private function storeAuctionBid(TransportRequest $transportRequest, User $user, float $bidAmount): void
    {
        /** @var Auction $auction */
        $auction = $transportRequest->auction()->get()->first();

        if (!$auction) {
            throw new \InvalidArgumentException('Transport request does not belong to any auction.');
        }

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
     * @return array<int, float> maps user_id to the price he has to pay for bought transport requests in the auction
     */
    private function evaluateBids(array $auctionedTransportRequests): array
    {
        $auctionPriceUserMap = [];

        foreach ($auctionedTransportRequests as $transportRequest) {
            $bids = $transportRequest->bids()->orderBy('bid_amount', 'desc')->get()->all();

            if (empty($bids)) {
                $transportRequest->markAsUnsold();
                continue;
            }

            $winningBid = $bids[0];

            if (count($bids) > 1) {
                $priceDefiningBid = $bids[1];
            } else {
                $priceDefiningBid = $bids[0];
            }

            $userId = (int)$winningBid['user_id'];
            if (array_key_exists($userId, $auctionPriceUserMap)) {
                $auctionPriceUserMap[$userId] += $priceDefiningBid['bid_amount'];
            } else {
                $auctionPriceUserMap[$userId] = $priceDefiningBid['bid_amount'];
            }

            /** @var User $winningCarrier */
            $winningCarrier = User::find($userId);
            $transportRequest->user()->associate($winningCarrier);
            $transportRequest->markAsSold();
            $transportRequest->save();
        }

        return $auctionPriceUserMap;
    }
}
