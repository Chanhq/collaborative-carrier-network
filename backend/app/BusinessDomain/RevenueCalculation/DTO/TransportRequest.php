<?php

namespace App\BusinessDomain\RevenueCalculation\DTO;

class TransportRequest
{
    public readonly int $originNode;
    public readonly int $targetNode;

    public function __construct(int $originNode, int $targetNode)
    {
        $this->originNode = $originNode;
        $this->targetNode = $targetNode;
    }
}
