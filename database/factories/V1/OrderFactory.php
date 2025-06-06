<?php

declare(strict_types=1);

namespace Database\Factories\V1;

use App\Enums\OrderDeliveryTypes;
use App\Models\V1\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Order>
 */
final class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'serial' => Str::random(16),
            'producer_id' => fake()->randomElement([1, 2, 3, 4]),
            'carrier_id' => fake()->randomElement([1, 2, 3, 4]),
            'branch_id' => fake()->randomElement([1, 2, 3, 4]),
            'transportation_id' => fake()->randomElement([1, 2, 3, 4]),
            'delivery_type' => fake()->randomElement(OrderDeliveryTypes::values()),
            'goods_price' => fake()->numberBetween(100, 500),
            'src_long' => fake()->longitude(),
            'src_lat' => fake()->latitude(),
            'dest_long' => fake()->longitude(),
            'dest_lat' => fake()->latitude(),
            'distance' => fake()->numberBetween(100, 500),
            'weight' => fake()->numberBetween(100, 500),
            'cost' => fake()->numberBetween(100, 500),
            'note' => fake()->word(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $order->createCode('pickup', 4);
            $order->createCode('delivered', 4);
        });
    }
}
