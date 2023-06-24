<?php

namespace App\BusinessDomain\Auction\Service;

use App\BusinessDomain\RevenueCalculation\Service\TransportCostCalculationService;
use App\BusinessDomain\RevenueCalculation\Service\TransportPriceCalculationService;
use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Exceptions\BusinessDomain\Auction\Exception\OngoingAuctionFoundException;
use App\Models\Auction;
use App\Models\Enum\AuctionStatusEnum;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\AuctionBid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
     * @throws OngoingAuctionFoundException
     * @throws \Throwable|OngoingAuctionFoundException
     */
    public function startAuction(): void
    {
        $selectedTransporRequests = [];

        DB::beginTransaction();
        try {
            $eligibleTransportRequests = $this->getTransportRequestEligibleForAuction();

            $startedAuction = new Auction();
            $startedAuction->save();

            foreach ($eligibleTransportRequests as $transportRequest) {
                $transportRequest->status = TransportRequestStatusEnum::Selected;
                $transportRequest->auction()->associate($startedAuction);
                $transportRequest->save();
                $this->submitBidsForCarriers($transportRequest);
            }

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

            $candidateRevenue =
               $this->priceCalculationService->calculatePriceForTransportRequest($candidateTransportRequest)
               - $this->costCalculationService->calculateTransportRequestCost(
                   $optimalPathWithCandidate,
                   $optimalPathWithoutCandidate
               );
            $a[] = $candidateRevenue;
            if ($candidateRevenue < self::REVENUE_THRESHOLD) {
                $eligibleTransportRequests[] = $candidateTransportRequest;
            }
        }
        //dd($a);
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
     * Calculate and submit bids for carriers
     *
     * @param TransportRequest $transportRequest
     */
    private function submitBidsForCarriers(TransportRequest $transportRequest): void
    {
        // Get the carriers eligible to bid on the transport request
        $eligibleCarriers = $this->getEligibleCarriers();

        foreach ($eligibleCarriers as $carrier) {
            // Calculate the worth of the transport request for the carrier
            $bidAmount = $this->calculateBidAmount($transportRequest, $carrier);

            // Submit the bid
            $this->submitBid($transportRequest, $carrier, $bidAmount);
        }
    }

    /**
     * Get the carriers eligible to bid on the transport request
     *
     * @return array<Carrier> // Should it be <User>?
     */
    private function getEligibleCarriers(): array
    {
        // Retrieve the eligible carriers from which folder

        // Return an array of Carrier objects.
    }

    /**
     * Calculate the worth of the transport request for the carrier
     *
     * @param TransportRequest $transportRequest
     * @param Carrier $carrier
     * @return float
     */
    private function calculateBidAmount(TransportRequest $transportRequest, Carrier $carrier): float
    {
        // Calculate the transport cost and price for the transport request
        $transportCost = $this->costCalculationService->calculateTransportRequestCost(
            $transportRequest->pathWithTransportRequest,
            $transportRequest->pathWithoutTransportRequest
        );
        $transportPrice = $this->priceCalculationService->calculatePriceForTransportRequest($transportRequest);

        // Adjust the bid amount based on the profitability or any other factors
        $profitability = $transportPrice - $transportCost;
        $bidAmount = $profitability * 0.8; // Should it be 80% of profitability

        return $bidAmount;
    }

    /**
     * Submit the bid for the transport request from the carrier
     *
     * @param TransportRequest $transportRequest
     * @param Carrier $carrier
     * @param float $bidAmount
     */
    private function submitBid(TransportRequest $transportRequest, Carrier $carrier, float $bidAmount): void
    {
        $auction = $transportRequest->auction;

        if (!$auction) {
            throw new \InvalidArgumentException('Transport request does not belong to any auction.');
        }

        // Create or update the bid for the carrier in the auction
        $bid = AuctionBid::updateOrCreate(
            ['auction_id' => $auction->id, 'carrier_id' => $carrier->id],
            ['bid_amount' => $bidAmount]
        );

        $bid->save();

    }
}
