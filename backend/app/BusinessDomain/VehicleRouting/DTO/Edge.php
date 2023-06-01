<?php

namespace App\BusinessDomain\VehicleRouting\DTO;

class Edge
{
    public readonly int $weight;
    public readonly int $source;
    public readonly int $target;

    public function __construct(int $weight, int $source, int $target)
    {
        $this->weight = $weight;
        $this->source = $source;
        $this->target = $target;
    }
}
