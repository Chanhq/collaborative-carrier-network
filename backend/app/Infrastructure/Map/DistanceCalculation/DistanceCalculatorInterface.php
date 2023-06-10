<?php

namespace App\Infrastructure\Map\DistanceCalculation;

use Fhaculty\Graph\Vertex;

interface DistanceCalculatorInterface
{
    public function calculateDistance(Vertex $vertex1, Vertex $vertex2): float;
}
