<?php

namespace App\Infrastructure\Map;

use App\Infrastructure\GraphML\GraphMlExporter;
use App\Infrastructure\Map\DistanceCalculation\DistanceCalculatorInterface;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class MapGenerator
{
    private readonly DistanceCalculatorInterface $distanceCalculator;

    public function __construct(DistanceCalculatorInterface $distanceCalculator)
    {
        $this->distanceCalculator = $distanceCalculator;
    }

    /**
     * Generates a graph and save it to the default map graphml with x and y coordinates
     */
    public function generateGraphWithCoords(): Graph
    {
        $newGraph = new Graph();
        $vertexId = 0;

        for ($row = 0; $row < 70; $row++) {
            $vertex = $newGraph->createVertex($vertexId);
            $vertex->setAttribute('y', random_int(1, 100));
            $vertex->setAttribute('x', random_int(1, 100));
            $vertexId++;
        }

        $edgeId = 0;
        foreach ($newGraph->getVertices()->getVector() as $vertex1) {
            foreach ($newGraph->getVertices()->getVector() as $vertex2) {
                if (
                    $vertex1->getId() !== $vertex2->getId()
                    && !$vertex1->hasEdgeTo($vertex2)
                    && !$vertex2->hasEdgeTo($vertex1)
                ) {
                    $edge = $vertex1->createEdge($vertex2);
                    $edge->setWeight($this->distanceCalculator->calculateDistance($vertex1, $vertex2));
                    $edge->setAttribute('id', $edgeId);
                    $edgeId++;
                }
            }
        }

        $exporter = new GraphMlExporter();
        file_put_contents('maps/test.graphml', $exporter->getOutput($newGraph));

        return $newGraph;
    }

    /**
     * Generates a graph and save it to the default map graphml, without x and y coordinates
     */
    public function generateConnectedGraph(int $vertexCount): Graph
    {
        $newGraph = new Graph();
        $vertexCount++;

        for ($i = 1; $i < $vertexCount; $i++) {
            $newGraph->createVertex($i);
        }

        $edgeId = 1;
        /** @var Vertex $currentVertex */
        foreach ($newGraph->getVertices() as $currentVertex) {
            $id = $currentVertex->getId();
            /** @var Vertex $otherVertex */
            foreach ($newGraph->getVertices() as $otherVertex) {
                if ($id === $otherVertex->getId()) {
                    continue;
                } else {
                    if (!$currentVertex->hasEdgeTo($otherVertex)) {
                        $edge = $currentVertex->createEdge($otherVertex);
                        $edge->setAttribute('weight', random_int(1, 100));
                        $edge->setAttribute('id', $edgeId++);
                    }
                }
            }
        }

        $exporter = new GraphMlExporter();
        file_put_contents('maps/default.graphml', $exporter->getOutput($newGraph));
        return $newGraph;
    }
}
