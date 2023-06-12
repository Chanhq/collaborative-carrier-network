<?php

namespace App\Http\Controllers;

use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;

class AuctioneerController extends Controller
{
    public function getForAuctionSelectedTransportRequests(): JsonResponse
    {
        $transportRequests =
            TransportRequest::all(['requester_name', 'origin_node', 'destination_node'])->toArray();

        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => [
                'transport_requests' => $transportRequests
            ]
        ]);
    }
}
