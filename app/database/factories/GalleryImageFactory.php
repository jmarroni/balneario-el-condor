<?php

namespace Database\Factories;

use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GalleryImageFactory extends Factory
{
    protected $model = GalleryImage::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Atardecer en la playa', 'Pescadores al amanecer', 'Faro histórico',
            'Costanera en verano', 'Olas del Atlántico', 'Flora local',
        ]) . ' ' . fake()->unique()->numberBetween(1, 9999);

        return [
            'title'         => $title,
            'slug'          => Str::slug($title),
            'description'   => fake()->optional()->sentence(),
            'path'          => 'gallery/' . fake()->uuid() . '.jpg',
            'thumb_path'    => 'gallery/thumbs/' . fake()->uuid() . '.jpg',
            'original_path' => 'gallery/original/' . fake()->uuid() . '.jpg',
            'taken_on'      => fake()->optional(0.6)->dateTimeBetween('-5 years', 'now')?->format('Y-m-d'),
            'views'         => fake()->numberBetween(0, 3000),
        ];
    }
}
