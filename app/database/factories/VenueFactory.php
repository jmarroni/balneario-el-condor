<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        $gourmetNames = ['La Cocina de Mar', 'Puerto Gourmet', 'El Rincón del Pescador', 'Parrilla Costera'];
        $nightNames   = ['Bar La Gaviota', 'Pub del Puerto', 'Sunset Lounge', 'Bodega Nocturna'];

        $category = fake()->randomElement(['gourmet', 'nightlife']);
        $name = fake()->randomElement($category === 'gourmet' ? $gourmetNames : $nightNames)
              . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'category'    => $category,
            'description' => fake()->paragraphs(2, true),
            'phone'       => '+54 9 2920 ' . fake()->numerify('######'),
            'address'     => fake()->streetAddress() . ', El Cóndor',
            'latitude'    => fake()->latitude(-41.2, -40.9),
            'longitude'   => fake()->longitude(-62.9, -62.6),
            'views'       => fake()->numberBetween(0, 1500),
        ];
    }
}
