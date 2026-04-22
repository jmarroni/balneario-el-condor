<?php

namespace Database\Factories;

use App\Models\Classified;
use App\Models\ClassifiedCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClassifiedFactory extends Factory
{
    protected $model = Classified::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Vendo', 'Alquilo', 'Busco', 'Ofrezco',
        ]) . ' ' . fake()->words(3, true);

        return [
            'classified_category_id' => ClassifiedCategory::factory(),
            'title'         => $title,
            'slug'          => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description'   => fake()->paragraphs(2, true),
            'contact_name'  => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'address'       => fake()->optional(0.7)->streetAddress(),
            'latitude'      => fake()->optional(0.5)->latitude(-41.2, -40.9),
            'longitude'     => fake()->optional(0.5)->longitude(-62.9, -62.6),
            'video_url'     => fake()->optional(0.1)->url(),
            'views'         => fake()->numberBetween(0, 500),
        ];
    }
}
