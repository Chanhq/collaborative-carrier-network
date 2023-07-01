<?php

namespace App\Console\Commands;

use App\Facades\Map;
use App\Infrastructure\GraphML\GraphMlExporter;
use App\Infrastructure\Map\MapGenerator;
use Graphp\GraphViz\GraphViz;
use Illuminate\Console\Command;

class VisualizeMap extends Command
{
    protected $signature = 'map:viz';
    protected $description = 'Visualizes the map of the application';
    private readonly GraphMlExporter $exporter;
    private readonly MapGenerator $mapGenerator;

    public function __construct(GraphMlExporter $exporter, MapGenerator $mapGenerator)
    {
        $this->exporter = $exporter;
        $this->mapGenerator = $mapGenerator;
        parent::__construct();
    }


    public function handle(): void
    {
        file_put_contents(
            'maps/default.graphml',
            $this->exporter->getOutput($this->mapGenerator->generateGraphWithCoords())
        );

        //$graphviz = new GraphViz();
        //$graphviz->display($graph);
    }
}
