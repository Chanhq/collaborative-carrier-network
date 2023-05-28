<?php

namespace App\Console\Commands;

use App\Facades\Map;
use Fhaculty\Graph\Graph;
use Graphp\GraphViz\GraphViz;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

class VisualizeMap extends Command
{
    protected $signature = 'map:viz';
    protected $description = 'Visualizes the map of the application';

    public function handle()
    {
        /** @var Graph $graph */
        $graph = Map::get();
        $maxOutgoingEdges = 0;

        /** @var Vertex $vertex */
        foreach (Map::vertices() as $vertex) {
            $this->info('N-Id: ' . $vertex->getId());
            $edgeCounter = 0;
            /** @var Base $edge */
            foreach ($vertex->getEdges() as $edge) {
                $this->info('E' . $edgeCounter . '-Id: ' . $edge->getAttribute('id'));
                $this->info('E' . $edgeCounter . '-W: ' . $edge->getWeight());
                $this->info('E' . $edgeCounter . '-S: ' . $edge->getAttribute('source'));
                $this->info('E' . $edgeCounter++ . '-T: ' . $edge->getAttribute('target'));
                $maxOutgoingEdges = $edgeCounter > $maxOutgoingEdges ? $edgeCounter : $maxOutgoingEdges;
            }
        }
        $this->info($maxOutgoingEdges);
        $graphviz = new GraphViz();
        $graphviz->display($graph);

        if (!Storage::exists('map.jpg')) {
            ImageManagerStatic::make($graphviz->createImageData($graph))->save('map.png');
        }
    }
}
