<?php

namespace App\Http\Controllers;

use App\BusinessDomain\Auction\Service\AuctionManagementService;
use App\Jobs\StartAuction;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;


class AuctioneerController extends Controller
{

    public function __construct(private readonly AuctionManagementService $auctionManagementService)
    {
    }

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
        StartAuction::dispatchAfterResponse();

        return new  JsonResponse([
           'status' => 'success',
            'message' => 'Successfully started auction creation process.',
            'data' => [],
        ]);
    }
}
