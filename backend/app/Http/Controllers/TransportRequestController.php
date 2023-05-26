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
                'origin_x' => $request->validated('origin_x'),
                'origin_y' => $request->validated('origin_x'),
                'destination_x' => $request->validated('destination_x'),
                'destination_y' => $request->validated('destination_y'),
            ]);

            Auth::user()->transportRequests()->save($transportRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Successfully added transport request for current user!',
                'data' => [
                    'requester_name' => $transportRequest->requester_name,
                    'origin_x' => $transportRequest->origin_x,
                    'origin_y' => $transportRequest->origin_y,
                    'destination_x' => $transportRequest->destination_x,
                    'destination_y' => $transportRequest->destination_y,
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
