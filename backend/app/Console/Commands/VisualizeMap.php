<?php

namespace App\Console\Commands;

use App\Facades\Map;
use App\Infrastructure\GraphML\GraphMlExporter;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\GraphViz\GraphViz;
use Illuminate\Console\Command;

class VisualizeMap extends Command
{
    protected $signature = 'map:viz';
    protected $description = 'Visualizes the map of the application';

    public function handle(): void
    {
        $graph = Map::get();

        $graphviz = new GraphViz();
        $graphviz->display($graph);
    }

    /**
     * Generates a graph and save it to the default map graphml with x and y coordinates
     */
    private function generateGraphWithCoords(): Graph
    {
        $newGraph = new Graph();
        $vertexId = 1;

        for ($row = 0; $row < 50; $row = $row+5) {
            for ($col = 0; $col < 25; $col = $col+5) {
                $vertex = $newGraph->createVertex($vertexId);
                $vertex->setAttribute('y', $row);
                $vertex->setAttribute('x', $col);
                $vertexId++;
            }
        }

        $edgeId = 1;
        foreach ($newGraph->getVertices()->getVector() as $vertex1) {
            foreach ($newGraph->getVertices()->getVector() as $vertex2) {
                if ($vertex1->getId() !== $vertex2->getId() && !$vertex1->hasEdgeTo($vertex2) && !$vertex2->hasEdgeTo($vertex1)) {
                    $edge = $vertex1->createEdge($vertex2);
                    $edge->setWeight($this->calculateDistance($vertex1, $vertex2));
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
    private function generateConnectedGraph(int $vertexCount): Graph
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
                        $edge->setAttribute('weight', random_int(1,100));
                        $edge->setAttribute('id', $edgeId++);
                    }
                }
            }
        }

        $exporter = new GraphMlExporter();
        file_put_contents('maps/default.graphml', $exporter->getOutput($newGraph));
        return $newGraph;
    }

    private function calculateDistance(Vertex $vertex1, Vertex $vertex2): int
    {
        $x1 = (int)$vertex1->getAttribute('x');
        $y1 = (int)$vertex1->getAttribute('y');

        $x2 = (int)$vertex2->getAttribute('x');
        $y2 = (int)$vertex2->getAttribute('y');

        return round(sqrt(pow($x2-$x1, 2) + pow($y2-$y1, 2)), 0)*10;
    }
}
