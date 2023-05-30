<?php

namespace App\Console\Commands;

use App\BusinessDomain\VehicleRouting\VehicleRoutingService;
use App\Models\User;
use Illuminate\Console\Command;

class OptimalPathForCarrierTransportRequestSet extends Command
{
    public function __construct(private readonly VehicleRoutingService $vehicleRoutingService)
    {
        parent::__construct();
    }
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

        $optimalPath = $this->vehicleRoutingService->findOptimalPath($user->transportRequests());

        dd($optimalPath);
    }
}
