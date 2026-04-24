<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyResponseFactory extends Factory
{
    protected $model = SurveyResponse::class;

    public function definition(): array
    {
        return [
            'survey_id'      => Survey::factory(),
            'option_key'     => fake()->numberBetween(1, 4),
            'comment'        => fake()->optional(0.3)->sentence(),
            'email'          => fake()->optional(0.4)->safeEmail(),
            'accepted_terms' => true,
            'ip_address'     => fake()->ipv4(),
        ];
    }
}
