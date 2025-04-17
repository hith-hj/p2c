<?php

namespace App\Enums;

enum OrderStatus : int
{
    case rejected = -2;
    case canceld = -1;
    case pending = 0;
    case assigned = 1;
    case picked = 2;
    case deliverd = 3;
}
