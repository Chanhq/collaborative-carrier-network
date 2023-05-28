<?php

namespace Database\Seeders;

use App\Facades\Map;
use App\Models\MapVertex;
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
        if(User::all()->count() === 0) {
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
    }
}
