<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransportRequestRequest;
use App\Models\TransportRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TransportRequestController extends Controller
{
    public function create(CreateTransportRequestRequest $request): JsonResponse
    {
        try {
            $transportRequest = new TransportRequest([
                'origin_node' => $request->validated('origin_node'),
                'destination_node' => $request->validated('destination_node'),
            ]);
            /** @var User $user */
            $user = Auth::user();
            $user->transportRequests()->save($transportRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Successfully added transport request for current user!',
                'data' => [
                    'origin_node' => $transportRequest->originNode(),
                    'destination_node' => $transportRequest->destinationNode(),
                ],
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unknown error occurred.',
                'data' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
