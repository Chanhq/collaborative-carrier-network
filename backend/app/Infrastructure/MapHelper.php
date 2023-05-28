<?php

namespace App\Infrastructure;

use App\Infrastructure\GraphML\GraphMlLoader;
use Fhaculty\Graph\Graph;

class MapHelper
{
    public function graph(): Graph
    {
        $data = file_get_contents('map.graphml');
        $loader = new GraphMlLoader();

        return $loader->loadContents($data);
    }
}
