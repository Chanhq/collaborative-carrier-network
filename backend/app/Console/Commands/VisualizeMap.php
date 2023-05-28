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
        $graph = Map::graph();

        $graphviz = new GraphViz();
        $graphviz->display($graph);

        if (!Storage::exists('map.jpg')) {
            ImageManagerStatic::make($graphviz->createImageData($graph))->save('map.png');
        }
    }
}
