<?php

namespace App\Console\Commands;

use App\BusinessDomain\VehicleRouting\VehicleRoutingService;
use App\Models\TransportRequest;
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
    protected $description = 'Dumps the optimal path for transport request set for a given carrier agent';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $carrierAgentId = (int)$this->argument('carrier');
        $user = User::find($carrierAgentId);

        if ($user === null || $user->isAuctioneer()) {
            $this->error('Could not find given user or is auctioneer! Aborting.');
            return self::FAILURE;
        }

        $transportRequests = [];

        /** @var TransportRequest $transportRequest */
        foreach ($user->transportRequests()->get() as $transportRequest) {
            $transportRequests[] = $transportRequest;
        }

        $optimalPath = $this->vehicleRoutingService->findOptimalPath($transportRequests);

        $this->table(['SOURCE', 'TARGET', 'WEIGHT'], array_map(function ($edge) {
            return [$edge->source, $edge->target, $edge->weight];
        }, $optimalPath));

        return self::SUCCESS;
    }
}
