<?php

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Transportation>
 */
class TransportationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstNameFemale(),
            'capacity' => fake()->numberBetween(30, 5000),
            'cost_per_km' => fake()->numberBetween(10, 20),
            'cost_per_kg' => fake()->numberBetween(1, 10),
            'initial_cost' => fake()->numberBetween(1, 10),
            'category' => fake()->randomElement(['bicycle', 'motorcycle', 'car', 'bickup', 'truck']),
        ];
    }
}
