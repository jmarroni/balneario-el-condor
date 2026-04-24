<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'email'      => fake()->safeEmail(),
            'phone'      => fake()->optional(0.7)->phoneNumber(),
            'subject'    => fake()->sentence(4),
            'message'    => fake()->paragraph(),
            'ip_address' => fake()->ipv4(),
            'read'       => fake()->boolean(60),
        ];
    }
}
