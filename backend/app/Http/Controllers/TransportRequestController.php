<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransportRequestRequest;
use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TransportRequestController extends Controller
{
    public function create(CreateTransportRequestRequest $request): JsonResponse
    {
        try {

            $transportRequest = new TransportRequest([
                'requester_name' => $request->validated('requester_name'),
                'origin' => $request->validated('origin'),
                'destination' => $request->validated('destination'),
            ]);

            Auth::user()->transportRequests()->save($transportRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Successfully added transport request for current user!',
                'data' => [
                    'requester_name' => $transportRequest->requester_name,
                    'origin' => $transportRequest->origin,
                    'destination' => $transportRequest->destination,
                ],
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unknown error occurred.',
                'data' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);        }
    }
}
