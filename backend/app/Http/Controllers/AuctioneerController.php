<?php

namespace App\Http\Controllers;

use App\Jobs\StartAuction;
use App\Models\Auction;
use App\Models\Enum\AuctionStatusEnum;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuctioneerController extends Controller
{
    public function getAuctionData(): JsonResponse
    {
        if (Auction::inactive()->get()->isNotEmpty()) {
            $transportRequests = TransportRequest::select(['id', 'origin_node', 'destination_node', 'status'])
                ->where('status', TransportRequestStatusEnum::Sold)
                ->orWhere('status', TransportRequestStatusEnum::Unsold)
                ->get()
                ->toArray();

            return new JsonResponse([
                'status' => 'success',
                'message' => '',
                'data' => [
                    'auction_status' => AuctionStatusEnum::Inactive,
                    'transport_requests' => $transportRequests,
                ]
            ]);
        }

        if (Auction::active()->get()->isNotEmpty()) {
            return new JsonResponse([
                'status' => 'success',
                'message' => '',
                'data' => [
                    'auction_status' => AuctionStatusEnum::Active,
                ]
            ]);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => [
                'auction_status' => AuctionStatusEnum::Completed,
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
