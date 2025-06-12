<?php

declare(strict_types=1);

return [
    'min_order_distance' => [
        'value' => '300',
        'description' => 'Minimum distance (in meters) required for an order to be valid',
    ],
    'max_order_distance' => [
        'value' => '50000',
        'description' => 'Maximum distance (in meters) allowed for an order to remain valid
',
    ],
    'order_attrs_calculation_type' => [
        'value' => 'total',
        'description' => 'Defines the method of calculation for order attributes:
total - Summarizes all attributes into a single value before adding it
byone - Adds each attribute separately to the total order value',
    ],
    'order_items_calculation_type' => [
        'value' => 'total',
        'description' => 'Defines the method of calculation for order items:
total - Summarizes all items into a single value before adding it
byone - Adds each attribute separately to the total order value',
    ],
    'urgent_order_cost' => [
        'value' => '20',
        'description' => 'Additional cost percent applied to orders marked as urgent
',
    ],
    'express_order_cost' => [
        'value' => '35',
        'description' => 'Additional cost percent applied to orders marked as express',
    ],
    'normal_delivery_time_per_km' => [
        'value' => '2',
        'description' => 'Estimated delivery time per kilometer (in hours) for normal deliveries',
    ],
    'urgent_delivery_time_per_km' => [
        'value' => '1',
        'description' => 'Estimated delivery time per kilometer (in hours) for urgent deliveries',
    ],
    'express_delivery_time_per_km' => [
        'value' => '0.5',
        'description' => 'Estimated delivery time per kilometer (in hours) for express deliveries',
    ],
    'fee_percent' => [
        'value' => '20',
        'description' => 'Percentage fee charged based on the total order cost',
    ],
    'delay_fee' => [
        'value' => '30',
        'description' => 'Additional fee percentage charged if the fee payment is delayed',
    ],
];
