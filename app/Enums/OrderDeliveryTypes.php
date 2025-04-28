<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderDeliveryTypes: string
{
    case normal = 'normal';
    case urgent = 'urgent';
    case express = 'express';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
