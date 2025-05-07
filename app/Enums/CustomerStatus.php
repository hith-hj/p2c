<?php

declare(strict_types=1);

namespace App\Enums;

enum CustomerStatus: int
{
    case blocked = -1;
    case fresh = 0;
    case normal = 1;
}
