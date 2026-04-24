<?php

namespace Database\Factories;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceProviderFactory extends Factory
{
    protected $model = ServiceProvider::class;

    public function definition(): array
    {
        $services = ['Plomería', 'Electricidad', 'Gasista', 'Albañilería', 'Jardinería', 'Carpintería', 'Pintura'];
        $name = fake()->randomElement($services) . ' ' . fake()->lastName();
        return [
            'name'          => $name,
            'slug'          => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description'   => fake()->paragraph(),
            'contact_name'  => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'phone'         => '+54 9 2920 ' . fake()->numerify('######'),
            'address'       => fake()->streetAddress() . ', El Cóndor',
            'latitude'      => fake()->latitude(-41.2, -40.9),
            'longitude'     => fake()->longitude(-62.9, -62.6),
        ];
    }
}
