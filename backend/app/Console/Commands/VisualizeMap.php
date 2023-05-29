<?php

namespace App\Console\Commands;

use App\Facades\Map;
use Fhaculty\Graph\Graph;
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
}
