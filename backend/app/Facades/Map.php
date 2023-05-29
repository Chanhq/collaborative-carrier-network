<?php

namespace App\Facades;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Vertices;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Graph get()
 * @method static Vertices vertices()
 */
class Map extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'map';
    }
}
