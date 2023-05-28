<?php

namespace App\Console\Commands;

use App\Infrastructure\GraphML\GraphMlLoader;
use Fhaculty\Graph\Edge\Base;
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
        $data = file_get_contents('map.graphml');

        $loader = new GraphMlLoader();
        $graph = $loader->loadContents($data);

        /** @var Base $edge */
        foreach ($graph->getEdges()->getIterator() as $edge) {
            $edge->setWeight((int)$edge->getAttribute('weight'));
        }

        $graphviz = new GraphViz();
        $graphviz->display($graph);

        if (!Storage::exists('map.jpg')) {
            ImageManagerStatic::make($graphviz->createImageData($graph))->save('map.png');
        }
    }
}
