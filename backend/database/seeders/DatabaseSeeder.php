<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        User::factory(1)->create(['is_auctioneer' => true]);
        User::factory(19)->create(['is_auctioneer' => false]);
    }
}
