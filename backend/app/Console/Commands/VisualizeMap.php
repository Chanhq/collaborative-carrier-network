<?php

namespace App\Console\Commands;

use App\Facades\Map;
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

    private function generateConnectedGraph(int $vertexCount)
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
    }
}
