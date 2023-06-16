<?php

namespace App\Jobs;

use App\BusinessDomain\Auction\Service\AuctionManagementService;
use App\Exceptions\BusinessDomain\Auction\Exception\OngoingAuctionFoundException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StartAuction implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $failOnTimeout = false;

    public int $timeout = 120000;

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

    public function failed(\Exception|OngoingAuctionFoundException $e): void
    {
        Log::critical($e->getMessage(), $e->getTrace());
    }
}
