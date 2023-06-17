<?php

namespace App\BusinessDomain\RevenueCalculation\Service;

use App\Facades\Map;
use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;
use App\Models\TransportRequest;
use App\Models\User;

class TransportPriceCalculationService
{
    private const BASE_PRICE = 20;
    private const PER_KILOMETER_PRICE = 2;

    private readonly DistanceCalculatorInterface $distanceCalculator;

    public function __construct(DistanceCalculatorInterface $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }

    public function calculatePriceForTransportRequest(TransportRequest $transportRequest, User $user): int
    {
        $pickupNode = $transportRequest->originNode();
        $deliveryNode = $transportRequest->destinationNode();
        $map = Map::get();

        $pickupVertex = $map->getVertex($pickupNode);
        $deliveryVertex = $map->getVertex($deliveryNode);
        $variablePricePart =
            $user->transportRequestPriceVariable() * $this->distanceCalculator->calculateDistance($pickupVertex, $deliveryVertex);

        return $user->transportRequestPriceBase() + $variablePricePart;
    }

    /**
     * @param TransportRequest[] $transportRequests
     */
    public function calculatePriceForTransportRequestSet(array $transportRequests, User $user): int
    {
        $cumCost = 0;

        foreach ($transportRequests as $transportRequest) {
            $cumCost += $this->calculatePriceForTransportRequest($transportRequest, $user);
        }

        return $cumCost;
    }
}
