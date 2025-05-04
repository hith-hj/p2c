<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phone = fake()->phoneNumber();

        return [
            'name' => fake()->firstNameFemale(),
            'phone' => $phone,
            'password' => Hash::make($phone),
            'status' => CustomerStatus::fresh->value,
        ];
    }
}
