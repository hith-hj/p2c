<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Branch>
 */
final class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'producer_id' => fake()->uuid,
            'name' => fake()->streetName(),
            'phone' => fake()->regexify('09[1-9]{1}\d{7}'),
            'is_default' => false,
        ];
    }
}
