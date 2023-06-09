<?php

namespace App\BusinessDomain\TransportPriceCalculator\Service;

use App\Infrastructure\MapGenerator;
use App\Models\TransportRequest;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class TransportPriceCalculatorService
{
    public static function calculatePrice(TransportRequest $request): float
    {
        $a1 = 20; // Base rate to reach the pickup point
        $a2 = 2;  // Price for 1 km multiplied with the distance between Pickup and Delivery

        $pickupNode = $request->originNode();
        $deliveryNode = $request->destinationNode();
        $distance = self::calculateDistance($pickupNode, $deliveryNode);

        $price = $a1 + ($a2 * $distance);
        return $price;
    }

    private static function calculateDistance(int $pickupNode, int $deliveryNode): float
    {
        $mapGenerator = new MapGenerator();
        $graph = $mapGenerator->generateGraphWithCoords();
        $pickupVertex = $graph->getVertex($pickupNode);
        $deliveryVertex = $graph->getVertex($deliveryNode);

        return self::calculateEuclideanDistance($pickupVertex, $deliveryVertex);
    }

    private static function calculateEuclideanDistance(Vertex $vertex1, Vertex $vertex2): float
    {
        $x1 = (int)$vertex1->getAttribute('x');
        $y1 = (int)$vertex1->getAttribute('y');

        $x2 = (int)$vertex2->getAttribute('x');
        $y2 = (int)$vertex2->getAttribute('y');

        return round(sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2)), 0) * 10;
    }
}
