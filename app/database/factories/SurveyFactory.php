<?php

namespace Database\Factories;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    public function definition(): array
    {
        return [
            'title'    => 'Encuesta ' . fake()->unique()->words(3, true),
            'question' => fake()->sentence() . '?',
            'options'  => [
                ['key' => 1, 'label' => 'Excelente'],
                ['key' => 2, 'label' => 'Bueno'],
                ['key' => 3, 'label' => 'Regular'],
                ['key' => 4, 'label' => 'Malo'],
            ],
            'active'   => true,
        ];
    }
}
