<?php

namespace Database\Factories;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        return [
            'email'              => fake()->unique()->safeEmail(),
            'status'             => 'confirmed',
            'confirmation_token' => Str::random(40),
            'subscribed_at'      => fake()->dateTimeBetween('-2 years', 'now'),
            'confirmed_at'       => fake()->dateTimeBetween('-2 years', 'now'),
            'ip_address'         => fake()->ipv4(),
        ];
    }
}
