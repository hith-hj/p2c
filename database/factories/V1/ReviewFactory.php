<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'belongTo_id' => 1,
            'belongTo_type' => 'App\Models\V1\Producer',
            'reviewer_id' => 1,
            'reviewer_type' => 'App\Models\V1\Carrier',
            'content' => fake()->paragraph(4),
            'rate' => fake()->numberBetween(1, 10),
        ];
    }
}
