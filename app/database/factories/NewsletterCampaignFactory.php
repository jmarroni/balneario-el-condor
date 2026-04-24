<?php

namespace Database\Factories;

use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterCampaignFactory extends Factory
{
    protected $model = NewsletterCampaign::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'sent']);
        return [
            'created_by_user_id' => User::factory(),
            'subject'            => fake()->sentence(6),
            'body_html'          => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'body_text'          => fake()->paragraphs(3, true),
            'status'             => $status,
            'scheduled_at'       => null,
            'sent_at'            => $status === 'sent' ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'sent_count'         => $status === 'sent' ? fake()->numberBetween(100, 1000) : 0,
        ];
    }
}
