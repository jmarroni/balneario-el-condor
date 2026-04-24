<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);
        return [
            'news_category_id' => NewsCategory::factory(),
            'title'            => $title,
            'slug'             => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'body'             => fake()->paragraphs(5, true),
            'video_url'        => fake()->boolean(20) ? fake()->url() : null,
            'published_at'     => fake()->dateTimeBetween('-2 years', 'now'),
            'views'            => fake()->numberBetween(0, 5000),
        ];
    }
}
