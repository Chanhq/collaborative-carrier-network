<?php

namespace App\BusinessDomain\RevenueCalculation\Service;

use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;

class TransportCostCalculatorService
{
    private readonly DistanceCalculatorInterface $distanceCalculator;

    public function __construct(DistanceCalculatorInterface $distanceCalculatorService)
    {
        $this->distanceCalculator = $distanceCalculatorService;
    }

    public function calculateTransportRequestCost(): float
    {
        return  1.0;
    }
}
