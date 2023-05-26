<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create 20 dummy users
        User::factory()->count(20)->create();
    }
}

