<?php

namespace Database\Factories;

use App\Models\Lodging;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LodgingFactory extends Factory
{
    protected $model = Lodging::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Hotel Faro', 'Cabañas del Mar', 'Hostería El Cóndor',
            'Camping Municipal', 'Casa de Doña Rosa', 'Posada Vista Bahía',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);
        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => fake()->paragraphs(2, true),
            'type'        => fake()->randomElement(['hotel', 'casa', 'camping', 'hostel']),
            'website'     => fake()->optional(0.4)->url(),
            'email'       => fake()->safeEmail(),
            'phone'       => '+54 9 2920 ' . fake()->numerify('######'),
            'address'     => fake()->streetAddress() . ', El Cóndor',
            'latitude'    => fake()->latitude(-41.2, -40.9),
            'longitude'   => fake()->longitude(-62.9, -62.6),
            'views'       => fake()->numberBetween(0, 2000),
        ];
    }
}
