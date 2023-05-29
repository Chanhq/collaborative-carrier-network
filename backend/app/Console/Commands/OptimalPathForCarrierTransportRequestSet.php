<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class OptimalPathForCarrierTransportRequestSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carrier:optimal-path {carrier=2 : Id of the carrier }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the optimal path for transport request set for a given carrier agent';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $carrierAgentId = $this->argument('carrier');
        $user = User::find($carrierAgentId);

        if ($user === null || $user->isAuctioneer()) {
            $this->error('Could not find given user or is auctioneer! Aborting.');
            return self::FAILURE;
        }

        $transportRequestsJson =
            json_encode($user->transportRequests()->get(['id', 'origin_node', 'destination_node'])->toArray());

        $result = Process::run('python3 network/main.py  --transportrequests ' . $transportRequestsJson);

        $this->info($result->output());

        return self::SUCCESS;
    }
}
