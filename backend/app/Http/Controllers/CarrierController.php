<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CarrierController extends Controller
{
    public function getMapData(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
