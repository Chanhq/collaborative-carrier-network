<?php

namespace App\BusinessDomain\VehicleRouting\DTO;

class Edge
{
    public readonly int $id;
    public readonly int $weight;
    public readonly int $source;
    public readonly int $target;

    public function __construct(int $id, int $weight, int $source, int $target)
    {
        $this->id = $id;
        $this->weight = $weight;
        $this->source = $source;
        $this->target = $target;
    }
}
