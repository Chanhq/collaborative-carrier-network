<?php

namespace App\Jobs;

use App\BusinessDomain\Auction\Exception\OngoingAuctionFoundException;
use App\BusinessDomain\Auction\Service\AuctionManagementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        } catch (OngoingAuctionFoundException | Throwable $e) {
            $this->failed($e);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::critical($e->getMessage(), $e->getTrace());
    }
}
