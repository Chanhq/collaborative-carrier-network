<?php

namespace App\Jobs;

use App\BusinessDomain\Auction\Service\AuctionManagementService;
use App\Exceptions\BusinessDomain\Auction\Exception\OngoingAuctionFoundException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StartAuction implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AuctionManagementService $auctionManagementService): void
    {
        try {
            $auctionManagementService->startAuction();
        } catch (OngoingAuctionFoundException $e) {
            $this->failed($e);
        }
    }

    private function failed(\Exception|OngoingAuctionFoundException $e): void
    {
        Log::critical($e->getMessage(), $e->getTrace());
    }
}
