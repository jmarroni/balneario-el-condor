<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Fiesta de la Primavera',
            'Fiesta del Tejo',
            'Encuentro de Pescadores',
            'Feria de Artesanos',
            'Festival de Música de Verano',
            'Torneo de Vóley Playa',
        ]) . ' ' . fake()->year();

        $starts = fake()->dateTimeBetween('-1 year', '+6 months');

        return [
            'title'                 => $title,
            'slug'                  => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description'           => fake()->paragraphs(3, true),
            'location'              => fake()->randomElement(['Costanera', 'Plaza Central', 'Playa Principal', 'Muelle']),
            'starts_at'             => $starts,
            'ends_at'               => (clone $starts)->modify('+3 hours'),
            'all_day'               => fake()->boolean(20),
            'featured'              => fake()->boolean(30),
            'accepts_registrations' => fake()->boolean(50),
            'sort_order'            => 0,
        ];
    }
}
