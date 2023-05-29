<?php

namespace App\Infrastructure;

use App\Infrastructure\GraphML\GraphMlLoader;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Vertices;

class MapHelper
{
    private Graph $map;

    public function __construct()
    {
        $data = file_get_contents('default.graphml');
        $loader = new GraphMlLoader();

        $this->map =$loader->loadContents($data);
    }

    public function get(): Graph
    {
        return $this->map;
    }

    public function vertices(): Vertices
    {
        return $this->map->getVertices();
    }
}
