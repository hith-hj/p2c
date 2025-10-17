<?php

declare(strict_types=1);

namespace App\Enums;

enum FeeTypes: int
{
    case normal = 1;
    case cancel = 2;
    case reject = 3;
    case delayed_payment = 4;
    case delayed_delivery = 5;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'names');
    }
}
