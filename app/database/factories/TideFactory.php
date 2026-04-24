<?php

namespace Database\Factories;

use App\Models\Tide;
use Illuminate\Database\Eloquent\Factories\Factory;

class TideFactory extends Factory
{
    protected $model = Tide::class;

    public function definition(): array
    {
        return [
            'location'            => 'El Cóndor',
            'date'                => fake()->unique()->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d'),
            'first_high'          => fake()->time('H:i:s'),
            'first_high_height'   => fake()->randomFloat(2, 2.0, 5.0) . ' m',
            'first_low'           => fake()->time('H:i:s'),
            'first_low_height'    => fake()->randomFloat(2, 0.2, 1.5) . ' m',
            'second_high'         => fake()->time('H:i:s'),
            'second_high_height'  => fake()->randomFloat(2, 2.0, 5.0) . ' m',
            'second_low'          => fake()->time('H:i:s'),
            'second_low_height'   => fake()->randomFloat(2, 0.2, 1.5) . ' m',
        ];
    }
}
