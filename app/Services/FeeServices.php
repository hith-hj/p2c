<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\V1\Fee;

final class FeeServices
{
    public function get(object $badge)
    {
        Truthy(! method_exists($badge, 'fees'), 'fees method missing');
        $fees = $badge->fees;
        NotFound($fees, 'Fees');

        return $fees;
    }

    public function find(int $id)
    {
        $fee = Fee::query()
            ->with(['subject', 'holder'])
            ->where('id', $id)
            ->first();
        NotFound($fee, 'Fee');

        return $fee;
    }
}
