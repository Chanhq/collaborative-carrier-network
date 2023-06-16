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
}
