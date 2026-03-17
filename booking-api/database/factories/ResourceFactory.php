<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                  => $this->faker->sentence(3),
            'description'           => $this->faker->paragraph(),
            'capacity'              => $this->faker->numberBetween(2, 20),
            'floor'                 => $this->faker->numberBetween(1, 10),
            'has_projector'         => $this->faker->boolean(70),
            'has_whiteboard'        => $this->faker->boolean(60),
            'has_video_conference'  => $this->faker->boolean(40),
            'has_monitor'           => $this->faker->boolean(50),
            'has_speakers'          => $this->faker->boolean(30),
            'price_per_hour'        => $this->faker->randomFloat(2, 500, 3000),
            'is_active'             => true,
        ];
    }
}