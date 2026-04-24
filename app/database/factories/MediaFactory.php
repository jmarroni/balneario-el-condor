<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'path'       => 'uploads/demo/' . fake()->uuid() . '.jpg',
            'alt'        => fake()->sentence(4),
            'type'       => 'image',
            'sort_order' => 0,
            // mediable_id y mediable_type se proveen al llamar al factory
        ];
    }
}
