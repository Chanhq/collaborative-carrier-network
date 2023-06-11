<?php

namespace App\BusinessDomain\RevenueCalculation\Service;

use App\Facades\Map;
use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;
use App\Models\TransportRequest;

class TransportPriceCalculationService
{
    private const BASE_PRICE = 20;
    private const PER_KILOMETER_PRICE = 2;

    private readonly DistanceCalculatorInterface $distanceCalculator;

    public function __construct(DistanceCalculatorInterface $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }

    public function calculatePriceForTransportRequest(TransportRequest $transportRequest): int
    {
        $pickupNode = $transportRequest->originNode();
        $deliveryNode = $transportRequest->destinationNode();
        $map = Map::get();

        $pickupVertex = $map->getVertex($pickupNode);
        $deliveryVertex = $map->getVertex($deliveryNode);
        $dynamicPricePart =
            self::PER_KILOMETER_PRICE * $this->distanceCalculator->calculateDistance($pickupVertex, $deliveryVertex);

        return self::BASE_PRICE + $dynamicPricePart;
    }

    /**
     * @param TransportRequest[] $transportRequests
     */
    public function calculatePriceForTransportRequestSet(array $transportRequests): int
    {
        $cumCost = 0;

        foreach ($transportRequests as $transportRequest) {
            $cumCost += $this->calculatePriceForTransportRequest($transportRequest);
        }

        return $cumCost;
    }
}
