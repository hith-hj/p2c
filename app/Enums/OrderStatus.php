<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: int
{
    case rejected = -2;
    case canceld = -1;
    case pending = 0;
    case assigned = 1;
    case picked = 2;
    case delivered = 3;
    case finished = 4;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
