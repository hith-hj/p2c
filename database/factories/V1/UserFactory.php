<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use App\Enums\UserRoles;
use App\Models\V1\Branch;
use App\Models\V1\Carrier;
use App\Models\V1\Location;
use App\Models\V1\Producer;
use App\Models\V1\Role;
use App\Models\V1\Transportation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'firebase_token' => Str::random(32),
            'role' => fake()->randomElement(UserRoles::cases()),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($user) {
            if ($user->role === 'producer') {
                Producer::factory()
                    ->for($user)
                    ->has(
                        Branch::factory()
                            ->has(Location::factory()->branch(), 'location'),
                        'branches'
                    )
                    ->create();
            } else {
                Carrier::factory()
                    ->for($user)
                    ->for(Transportation::factory(), 'transportation')
                    ->has(Location::factory()->carrier(), 'location')
                    ->create();
            }
        });
    }
}
