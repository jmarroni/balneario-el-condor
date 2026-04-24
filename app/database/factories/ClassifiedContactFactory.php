<?php

namespace Database\Factories;

use App\Models\Classified;
use App\Models\ClassifiedContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassifiedContactFactory extends Factory
{
    protected $model = ClassifiedContact::class;

    public function definition(): array
    {
        return [
            'classified_id'     => Classified::factory(),
            'contact_name'      => fake()->name(),
            'contact_email'     => fake()->safeEmail(),
            'contact_phone'     => '+54 9 2920 ' . fake()->numerify('######'),
            'message'           => fake()->paragraph(),
            'destination_email' => fake()->safeEmail(),
            'ip_address'        => fake()->ipv4(),
        ];
    }
}
