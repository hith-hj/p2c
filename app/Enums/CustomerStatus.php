<?php

declare(strict_types=1);

namespace App\Enums;

enum CustomerStatus: int
{
    case blocked = -1;
    case fresh = 0;
    case normal = 1;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'names');
    }
}
