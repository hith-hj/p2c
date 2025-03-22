<?php

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    public function producer()
    {
        return $this->state([
            'name' => 'producer',
        ]);
    }

    public function carrier()
    {
        return $this->state([
            'name' => 'carrier',
        ]);
    }
}
