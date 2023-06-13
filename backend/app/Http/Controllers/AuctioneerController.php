<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Enum\AuctionStatusEnum;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


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
        $activeAuction = Auction::active('status', AuctionStatusEnum::Active->value);

        if ($activeAuction !== null) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'There can only be one active auction at a time.',
                'data' => [],
            ], Response::HTTP_CONFLICT);
        }

        return new JsonResponse([
           'test' => 'test',
        ]);
    }
}
