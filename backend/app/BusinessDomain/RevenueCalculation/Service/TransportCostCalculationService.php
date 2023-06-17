<?php

namespace App\BusinessDomain\RevenueCalculation\Service;

use App\BusinessDomain\VehicleRouting\DTO\Edge;
use App\Models\User;

class TransportCostCalculationService
{
    /**
     * @param Edge[] $pathWithTransportRequest
     * @param Edge[] $pathWithoutTransportRequest
     */
    public function calculateTransportRequestCost(
        array $pathWithTransportRequest,
        array $pathWithoutTransportRequest,
        User $user,
    ): int {
        $lengthDifference = $this->calculateLengthOfPath($pathWithTransportRequest)
            - $this->calculateLengthOfPath($pathWithoutTransportRequest);

        return $user->transportRequestCostBase() + $lengthDifference * $user->transportRequestCostVariable();
    }

    /**
     * @param Edge[] $path
     * @return int
     */
    public function calculateTotalCostOfPath(array $path, User $user): int
    {
        return \count($path) * $user->transportRequestCostBase()
            + $this->calculateLengthOfPath($path) * $user->transportRequestCostVariable();
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
