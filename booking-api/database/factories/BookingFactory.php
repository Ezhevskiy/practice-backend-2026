<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'resource_id' => Resource::factory(),
            'starts_at'   => $this->faker->dateTimeBetween('now', '+3 days'),
            'ends_at'     => $this->faker->dateTimeBetween('+3 hours', '+6 hours'),
            'status'      => 'confirmed',
            'notes'       => $this->faker->sentence(),
        ];
    }
}