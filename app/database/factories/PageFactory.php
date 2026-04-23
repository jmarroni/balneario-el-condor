<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Historia del Balneario',
            'Fauna local',
            'Lugares imperdibles',
            'Servicios disponibles',
            'Cómo llegar',
            'Preguntas frecuentes',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'slug'             => Str::slug($title),
            'title'            => $title,
            'content'          => fake()->paragraphs(5, true),
            'meta_description' => fake()->sentence(12),
            'published'        => true,
        ];
    }
}
