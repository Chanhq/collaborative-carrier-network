<?php

use App\Services\TransportPriceCalculatorService;
use App\Models\TransportRequest;
use PHPUnit\Framework\TestCase;

class TransportPriceCalculatorServiceTest extends TestCase
{
    public function testCalculatePrice()
    {
        $request = new TransportRequest(1, 5); // Assuming pickup node is 1 and delivery node is 5
        $price = TransportPriceCalculatorService::calculatePrice($request);

        // Assert that the calculated price is as expected
        $this->assertEquals(120, $price);
    }

    public function testCalculateDistance()
    {
        // Create a sample graph
        $graph = new Fhaculty\Graph\Graph();
        $pickupNode = $graph->createVertex(1);
        $deliveryNode = $graph->createVertex(5);

        // Set x and y coordinates for the vertices
        $pickupNode->setAttribute('x', 0);
        $pickupNode->setAttribute('y', 0);
        $deliveryNode->setAttribute('x', 10);
        $deliveryNode->setAttribute('y', 0);

        // Calculate the distance between the vertices
        $distance = TransportPriceCalculatorService::calculateDistance(1, 5);

        // Assert that the calculated distance is as expected
        $this->assertEquals(100, $distance);
    }

    public function testCalculateEuclideanDistance()
    {
        $vertex1 = new Fhaculty\Graph\Vertex(1);
        $vertex1->setAttribute('x', 0);
        $vertex1->setAttribute('y', 0);

        $vertex2 = new Fhaculty\Graph\Vertex(2);
        $vertex2->setAttribute('x', 3);
        $vertex2->setAttribute('y', 4);

        $distance = TransportPriceCalculatorService::calculateEuclideanDistance($vertex1, $vertex2);

        // Assert that the calculated distance is as expected
        $this->assertEquals(50, $distance);
    }
}
