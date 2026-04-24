<?php

namespace Database\Factories;

use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RentalFactory extends Factory
{
    protected $model = Rental::class;

    public function definition(): array
    {
        $title = 'Alquiler ' . fake()->randomElement(['temporal', 'mensual', 'fin de semana', 'de temporada'])
               . ' ' . fake()->unique()->numberBetween(1, 999);
        return [
            'title'        => $title,
            'slug'         => Str::slug($title),
            'places'       => fake()->numberBetween(2, 10),
            'contact_name' => fake()->name(),
            'phone'        => '+54 9 2920 ' . fake()->numerify('######'),
            'email'        => fake()->safeEmail(),
            'address'      => fake()->streetAddress() . ', El Cóndor',
            'description'  => fake()->paragraph(),
        ];
    }
}
