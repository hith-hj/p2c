<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'long' => fake()->longitude(31, 31),
            'lat' => fake()->latitude(31, 31),
        ];
    }

    public function carrier()
    {
        return $this->state([
            'belongTo_type' => 'App\Models\V1\Carrier',
        ]);
    }

    public function branch()
    {
        return $this->state([
            'belongTo_type' => 'App\Models\V1\Branch',
        ]);
    }
}
