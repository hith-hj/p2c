<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Transportation>
 */
final class TransportationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = fake()->numberBetween(10, 20);

        return [
            'name' => fake()->firstNameFemale(),
            'capacity' => fake()->numberBetween(300, 5000),
            'cost_per_kg' => $number,
            'cost_per_km' => $number * 4,
            'initial_cost' => $number * 1.5,
            'cancel_cost' => $number * 3,
            'category' => fake()->randomElement(['bicycle', 'motorcycle', 'car', 'bickup', 'truck']),
        ];
    }
}
