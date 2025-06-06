<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Carrier>
 */
final class CarrierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->uuid,
            'transportation_id' => fake()->unique()->numberBetween(1, 100),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'rate' => fake()->numberBetween(1, 10),
            'is_valid' => true,
        ];
    }
}
