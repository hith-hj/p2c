<?php

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
            'long' => fake()->longitude(),
            'lat' => fake()->latitude(),
        ];
    }

    public function carrier()
    {
        return $this->state([
            'locatable_type' => 'App\Models\V1\Carrier',
        ]);
    }

    public function branch()
    {
        return $this->state([
            'locatable_type' => 'App\Models\V1\Branch',
        ]);
    }
}
