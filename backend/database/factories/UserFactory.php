<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'password' => Hash::make('password123'),
            'is_auctioneer' => $this->faker->boolean,
            'transport_request_minimum_revenue' => 70,
            'transport_request_cost_base' => 10,
            'transport_request_cost_variable' => 1,
            'transport_request_price_base' => 20,
            'transport_request_price_variable' => 2,
        ];
    }
}
