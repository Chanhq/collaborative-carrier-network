<?php

namespace App\Infrastructure;

use App\Infrastructure\GraphML\GraphMlExporter;
use App\Infrastructure\GraphML\GraphMlLoader;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Vertices;

class MapHelper
{
    private Graph $map;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $data = file_get_contents('maps/default.graphml');

        if ($data === false) {
            throw new \RuntimeException('Could not load graphml file of map');
        }

        $loader = new GraphMlLoader();

        $this->map = $loader->loadContents($data);
    }

    public function get(): Graph
    {
        return $this->map;
    }

    public function vertices(): Vertices
    {
        return $this->map->getVertices();
    }

    public function xml(): string
    {
        return (new GraphMlExporter())->getOutput($this->map);
    }
}
