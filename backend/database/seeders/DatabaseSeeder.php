<?php

namespace Database\Seeders;

use App\BusinessDomain\RevenueCalculation\Service\TransportPriceCalculationService;
use App\BusinessDomain\VehicleRouting\PythonVehicleRoutingWrapper;
use App\Facades\Map;
use App\Models\MapVertex;
use App\Models\TransportRequest;
use App\Models\User;
use Fhaculty\Graph\Vertex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function __construct(
        private readonly PythonVehicleRoutingWrapper $vehicleRoutingService,
        private readonly TransportPriceCalculationService $priceCalculationService,
    ){
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $output = $this->command->getOutput();
        if (User::all()->count() === 0) {
            User::factory(1)->create(['username' => 'auctioneer', 'is_auctioneer' => true]);
            User::factory(1)->create(['username' => 'carrier1', 'is_auctioneer' => false]);
            User::factory(1)->create(['username' => 'carrier2', 'is_auctioneer' => false]);
            User::factory(1)->create(['username' => 'carrier3', 'is_auctioneer' => false]);
        }

        $mapVertices = Map::vertices();
        /** @var Vertex $vertex */
        foreach ($mapVertices as $vertex) {
            $id = Str::remove(search: 'n', subject: (string)$vertex->getId());
            if (MapVertex::find($id) === null) {
                MapVertex::factory(1)->create(['id' => $id]);
            }
        }

        if (TransportRequest::all()->count() === 0) {
            $userBar = $output->createProgressBar(count(User::all()) - 1);
            $userBar->start();
            /** @var User $user */
            foreach (User::all() as $user) {
                if ($user->isAuctioneer()) {
                    continue;
                }

                $transportRequests = [];
                $trBar = $output->createProgressBar(3);
                $trBar->start();
                while (count($transportRequests) < 3) {
                    // Ids start at 1, node with id 1 is depot so TRs should not start there
                    $origin_node_id = random_int(2, MapVertex::max('id'));
                    do {
                        // Ids start at 1, node with id 1 is depot so TRs should not end there
                        $destination_node_id = random_int(2, MapVertex::max('id'));
                    } while ($origin_node_id === $destination_node_id);
                    $transportRequest = new TransportRequest([
                        'origin_node' => $origin_node_id,
                        'destination_node' => $destination_node_id,
                    ]);
                    if (
                        $this->vehicleRoutingService->hasOptimalPath(
                            array_merge($transportRequests, [$transportRequest])
                        )
                    ) {
                        $transportRequests[] = $transportRequest;
                        $output->info('Got feasible tr, adding');
                        $trBar->advance();
                    } else {
                        $output->info('Got infeasible tr, trying new without adding');
                    }
                }
                $trBar->finish();
                $user->transportRequests()->saveMany($transportRequests);

                $user->transport_request_set_revenue =
                    $this->priceCalculationService->calculatePriceForTransportRequestSet(
                        $transportRequests,
                        $user
                    );
                $user->save();

                $userBar->advance();
            }
            $userBar->finish();
        }
    }
}
