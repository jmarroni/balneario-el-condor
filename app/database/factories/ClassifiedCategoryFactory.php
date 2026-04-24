<?php

namespace Database\Factories;

use App\Models\ClassifiedCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClassifiedCategoryFactory extends Factory
{
    protected $model = ClassifiedCategory::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Inmuebles', 'Vehículos', 'Empleos',
            'Servicios', 'Objetos', 'Mascotas',
        ]);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
        ];
    }
}
