<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportRequestController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        return new JsonResponse($request->toArray());
    }
}
