<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DummyController extends Controller
{
    public function test(Request $request): JsonResponse
    {
        return new JsonResponse($request->toArray());
    }
}
