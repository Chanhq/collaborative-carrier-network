<?php

namespace App\Http\Controllers;

use App\Jobs\StartAuction;
use App\Models\Auction;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuctioneerController extends Controller
{
    public function getForAuctionSelectedTransportRequests(): JsonResponse
    {
        $transportRequests = TransportRequest::select(['id', 'origin_node', 'destination_node', 'status'])
            ->where('status', TransportRequestStatusEnum::Sold)
            ->orWhere('status', TransportRequestStatusEnum::Unsold)
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
        $activeAuction = Auction::active()->get()->first();

        if ($activeAuction !== null) {
            return new  JsonResponse([
                'status' => 'error',
                'message' => 'There is already an ongoing auction.',
                'data' => [],
            ], Response::HTTP_CONFLICT);
        }

        StartAuction::dispatch();

        return new  JsonResponse([
           'status' => 'success',
            'message' => 'Successfully started auction creation process.',
            'data' => [],
        ]);
    }
}
