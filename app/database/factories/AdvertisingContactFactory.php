<?php

namespace Database\Factories;

use App\Models\AdvertisingContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertisingContactFactory extends Factory
{
    protected $model = AdvertisingContact::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email'     => fake()->safeEmail(),
            'message'   => fake()->paragraph(),
            'zone'      => fake()->randomElement(['home-top', 'sidebar', 'footer', 'events-page']),
            'read'      => fake()->boolean(50),
        ];
    }
}
