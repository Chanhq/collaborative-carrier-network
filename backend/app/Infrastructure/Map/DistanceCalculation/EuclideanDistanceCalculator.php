<?php

namespace App\Infrastructure\Map\DistanceCalculation;

use Fhaculty\Graph\Vertex;

class EuclideanDistanceCalculator implements DistanceCalculatorInterface
{

    public function calculateDistance(Vertex $vertex1, Vertex $vertex2): float
    {
        $x1 = (int)$vertex1->getAttribute('x');
        $y1 = (int)$vertex1->getAttribute('y');

        $x2 = (int)$vertex2->getAttribute('x');
        $y2 = (int)$vertex2->getAttribute('y');

        return round(sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2)), 0) * 10;
    }
}
