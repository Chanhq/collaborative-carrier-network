<?php

namespace App\Jobs;

use App\BusinessDomain\Auction\Exception\OngoingAuctionFoundException;
use App\BusinessDomain\Auction\Service\AuctionManagementService;
use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
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
            $startedAuction = new Auction();
            $startedAuction->save();

            $auctionPriceUserMap = $auctionManagementService->auctionTransportRequests($startedAuction);

            $auctionManagementService->evaluateAuction($auctionPriceUserMap);

            /** @var Collection<Auction> $activeAuctionCollection */
            $activeAuctionCollection = Auction::active()->get();
            /** @var Auction $currentlyOngoingAuction */
            $currentlyOngoingAuction = $activeAuctionCollection->first();
            $currentlyOngoingAuction->setInactive();
        } catch (OngoingAuctionFoundException | Throwable $e) {
            $this->failed($e);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::critical($e->getMessage(), $e->getTrace());
    }
}
