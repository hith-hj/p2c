<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRoles: string
{
    case Producer = 'producer';
    case Carrier = 'carrier';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'names');
    }
}
