<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Mejillones al vapor', 'Calamares rellenos', 'Pescado a la parrilla',
            'Torta de algas', 'Empanadas de mariscos', 'Sopa de pescado',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'title'        => $title,
            'slug'         => Str::slug($title),
            'prep_minutes' => fake()->numberBetween(10, 60),
            'cook_minutes' => fake()->numberBetween(15, 120),
            'servings'     => fake()->randomElement(['2 porciones', '4 porciones', '6 porciones', '8-10 porciones']),
            'cost'         => fake()->randomElement(['Bajo', 'Medio', 'Alto']),
            'ingredients'  => "- Ingrediente 1\n- Ingrediente 2\n- Ingrediente 3",
            'instructions' => fake()->paragraphs(3, true),
            'author'       => fake()->name(),
            'published_on' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        ];
    }
}
