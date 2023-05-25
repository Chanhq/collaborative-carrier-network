<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransportRequestRequest;
use Illuminate\Http\JsonResponse;

class TransportRequestController extends Controller
{
    public function create(CreateTransportRequestRequest $request): JsonResponse
    {
        return new JsonResponse($request->toArray());
    }
}
