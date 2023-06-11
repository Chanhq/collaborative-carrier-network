<?php

namespace App\BusinessDomain\RevenueCalculation\Service;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;

class TransportCostCalculationService
{
    private const BASE_COST = 10;

    private const PER_KILOMETER_COST = 1;

    /**
     * @param Edge[] $optimalPathWithTransportRequest
     * @param Edge[] $optimalPathWithoutTransportRequest
     */
    public function calculateTransportRequestCost(
        array $optimalPathWithTransportRequest,
        array $optimalPathWithoutTransportRequest
    ): int {
        $lengthDifference = $this->calculateLengthOfPath($optimalPathWithTransportRequest)
            - $this->calculateLengthOfPath($optimalPathWithoutTransportRequest);

        return self::BASE_COST + $lengthDifference * self::PER_KILOMETER_COST;
    }

    /**
     * @param Edge[] $path
     * @return int
     */
    public function calculateTotalCostOfPath(array $path): int
    {
        return \count($path) * self::BASE_COST + $this->calculateLengthOfPath($path) * self::PER_KILOMETER_COST;
    }

    /**
     * @param Edge[] $path
     */
    private function calculateLengthOfPath(array $path): int
    {
        $cumLength = 0;

        foreach ($path as $edge) {
            $cumLength += $edge->weight;
        }

        return $cumLength;
    }
}
