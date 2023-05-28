<?php

namespace Database\Seeders;

use App\Facades\Map;
use App\Models\MapVertex;
use App\Models\TransportRequest;
use App\Models\User;
use Fhaculty\Graph\Vertex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        if (User::all()->count() === 0) {
            User::factory(1)->create(['is_auctioneer' => true]);
            User::factory(19)->create(['is_auctioneer' => false]);
        }

        $mapVertices = Map::vertices();
        /** @var Vertex $vertex */
        foreach ($mapVertices as $vertex) {
            $id = Str::remove(search: 'n', subject: $vertex->getId());
            if (MapVertex::find($id) === null) {
                MapVertex::factory(1)->create(['id' => $id]);
            }
        }

        if (TransportRequest::all()->count() === 0) {
            /** @var User $user */
            foreach (User::all() as $user) {
                if ($user->isAuctioneer()){
                    continue;
                }

                $transportRequests = [];
                $destination_node_id = 0;
                $origin_node_id = 0;

                for ($i = 0; $i < 10; $i++) {
                    do{
                        $origin_node_id = random_int(0, 2576);
                        do {
                            $destination_node_id = random_int(0, 2576);
                        } while ($origin_node_id === $destination_node_id);
                    } while (in_array([$origin_node_id, $destination_node_id], $transportRequests));
                    $transportRequest = new TransportRequest([
                        'requester_name' => 'Some requester name',
                        'origin_node' => $origin_node_id,
                        'destination_node' => $destination_node_id,
                    ]);
                    $user->transportRequests()->save($transportRequest);
                    $transportRequests[] = [$origin_node_id, $destination_node_id];
                }
            }
        }
    }
}
