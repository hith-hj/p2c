<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'belongTo_id' => 1,
            'belongTo_type' => 'producer',
            'status' => 0,
            'title' => 'Title',
            'body' => 'Body',
            'type' => fake()->randomElement([0, 1, 2, 3]),
            'payload' => json_encode([
                'extra' => 'this is extra shit',
            ]),
        ];
    }
}
