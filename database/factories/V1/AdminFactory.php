<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Attr>
 */
final class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->email(),
            'phone' => fake()->regexify("(09)[1-9]{1}\d{7}"),
            'password' => Hash::make('password'),
        ];
    }
}
