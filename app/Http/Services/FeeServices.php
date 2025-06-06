<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\V1\Fee;
use App\Traits\ExceptionHandler;

final class FeeServices
{
    use ExceptionHandler;

    public function get(object $badge)
    {
        $this->Truthy(! method_exists($badge, 'fees'), 'fees method missing');
        $fees = $badge->fees;
        $this->NotFound($fees, 'Fees');

        return $fees;
    }

    public function find(int $id)
    {
        $fee = Fee::query()
            ->with(['subject', 'holder'])
            ->where('id', $id)
            ->first();
        $this->NotFound($fee, 'Fee');

        return $fee;
    }
}
