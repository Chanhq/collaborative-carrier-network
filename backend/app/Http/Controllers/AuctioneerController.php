<?php

namespace App\Http\Controllers;

use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;

class AuctioneerController extends Controller
{
    public function getForAuctionSelectedTransportRequests(): JsonResponse
    {
        $transportRequests = TransportRequest::select(['requester_name', 'origin_node', 'destination_node'])
            ->where('status', TransportRequestStatusEnum::Selected)
            ->get()
            ->toArray();

        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => [
                'transport_requests' => $transportRequests
            ]
        ]);
    }

    public function startAuction(): JsonResponse
    {
        return new JsonResponse([
           'test' => 'test',
        ]);
    }
}
