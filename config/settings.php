<?php

declare(strict_types=1);

return [
    // Minimum distance (in meters) required for an order to be valid
    'min_order_distance' => 300,

    // Maximum distance (in meters) allowed for an order to remain valid
    'max_order_distance' => 50000,

    // Defines the method of calculation for order attributes:
    // 'total' - Summarizes all attributes into a single value before adding it
    // 'byone' - Adds each attribute separately to the total order value
    'order_attrs_calculation_type' => 'total',

    // Defines the method of calculation for order items:
    // 'total' - Summarizes all items into a single value before adding it
    // 'byone' - Adds each attribute separately to the total order value
    'order_items_calculation_type' => 'total',

    // Additional cost percent applied to orders marked as 'urgent'
    'urgent_order_cost' => 20,

    // Additional cost percent applied to orders marked as 'express'
    'express_order_cost' => 35,

    // Estimated delivery time per kilometer (in hours) for normal deliveries
    'normal_delivery_time_per_km' => 2,

    // Estimated delivery time per kilometer (in hours) for urgent deliveries
    'urgent_delivery_time_per_km' => 1,

    // Estimated delivery time per kilometer (in hours) for express deliveries
    'express_delivery_time_per_km' => 0.5,

    // Percentage fee charged based on the total order cost
    'fee_percent' => 20,

    // Additional fee percentage charged if the fee payment is delayed
    'delay_fee' => 30,
];
