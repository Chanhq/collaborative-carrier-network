<?php

namespace App\Http\Controllers;

use App\Jobs\StartAuction;
use App\Models\Auction;
use App\Models\Enum\AuctionStatusEnum;
use App\Models\Enum\TransportRequestStatusEnum;
use App\Models\TransportRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuctioneerController extends Controller
{
    public function getAuctionData(): JsonResponse
    {
        /** @var Collection $inactiveAuctions */
        $inactiveAuctions = Auction::inactive()->get();

        if ($inactiveAuctions->isNotEmpty()) {
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

        /** @var Collection $activeAuctions */
        $activeAuctions = Auction::active()->get();

        if ($activeAuctions->isNotEmpty()) {
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
        /** @var Collection $activeAuctions */
        $activeAuctions = Auction::active()->get();
        /** @var Collection $inActiveAuctions */
        $inActiveAuctions = Auction::inactive()->get();

        if ($activeAuctions->first() !== null || $inActiveAuctions->first() !== null) {
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

    public function endAuction(): JsonResponse
    {
        $inActiveAuctionModel = Auction::inactive();
        /** @var Collection $inactiveAuctionCollection */
        $inactiveAuctionCollection = $inActiveAuctionModel->get();

        if ($inactiveAuctionCollection->first() === null) {
            return new  JsonResponse([
                'status' => 'error',
                'message' => 'There is no auction to be ended.',
                'data' => [],
            ], Response::HTTP_CONFLICT);
        }

        $inActiveAuctionModel->update(['status' => AuctionStatusEnum::Completed]);

        return new JsonResponse([
            'status' => 'success',
            'message' => '',
            'data' => [],
        ]);
    }
}
