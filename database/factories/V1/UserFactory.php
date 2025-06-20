<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use App\Enums\UserRoles;
use App\Models\V1\Branch;
use App\Models\V1\Carrier;
use App\Models\V1\Location;
use App\Models\V1\Producer;
use App\Models\V1\Transportation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\User>
 */
final class UserFactory extends Factory
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
            'phone' => fake()->regexify('09[1-9]{1}\d{7}'),
            'verified_at' => now(),
            'password' => self::$password ??= Hash::make('password'),
            'firebase_token' => Str::random(32),
            'role' => fake()->randomElement(UserRoles::values()),
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
