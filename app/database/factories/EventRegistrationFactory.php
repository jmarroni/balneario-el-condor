<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        return [
            'event_id'   => Event::factory(),
            'name'       => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'email'      => fake()->safeEmail(),
            'phone'      => fake()->phoneNumber(),
            'province'   => fake()->randomElement(['Río Negro', 'Buenos Aires', 'La Pampa', 'Neuquén']),
            'city'       => fake()->city(),
            'extra_data' => [
                'attendees'   => fake()->numberBetween(1, 5),
                'accommodation' => fake()->boolean(),
            ],
            'comments'   => fake()->optional(0.3)->sentence(),
            'ip_address' => fake()->ipv4(),
        ];
    }
}
