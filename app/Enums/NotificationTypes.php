<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationTypes: int
{
    case normal = 0;
    case verification = 1;
    case order = 2;
    case fee = 3;
}
