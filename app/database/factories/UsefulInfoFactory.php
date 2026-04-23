<?php

namespace Database\Factories;

use App\Models\UsefulInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsefulInfoFactory extends Factory
{
    protected $model = UsefulInfo::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Policía', 'Bomberos', 'Hospital', 'Defensa Civil',
            'Municipalidad', 'Turismo Municipal', 'Guardavidas',
        ]);

        return [
            'title'      => $title,
            'phone'      => fake()->boolean(70) ? fake()->randomElement(['911', '100', '107']) : '+54 9 2920 ' . fake()->numerify('######'),
            'website'    => fake()->optional(0.3)->url(),
            'email'      => fake()->optional(0.5)->safeEmail(),
            'address'    => fake()->streetAddress() . ', El Cóndor',
            'latitude'   => fake()->latitude(-41.2, -40.9),
            'longitude'  => fake()->longitude(-62.9, -62.6),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
