<?php

namespace Database\Factories;

use App\Models\NearbyPlace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NearbyPlaceFactory extends Factory
{
    protected $model = NearbyPlace::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Playa La Lobería', 'Bahía Creek', 'Viedma', 'Carmen de Patagones',
            'Faro Segunda Barranca', 'Río Negro - desembocadura',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'title'       => $title,
            'slug'        => Str::slug($title),
            'description' => fake()->paragraph(),
            'address'     => fake()->optional()->streetAddress(),
            'latitude'    => fake()->latitude(-41.2, -40.6),
            'longitude'   => fake()->longitude(-63.0, -62.5),
            'views'       => fake()->numberBetween(0, 1500),
        ];
    }
}
