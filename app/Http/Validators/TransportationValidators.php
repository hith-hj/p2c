<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

final class TransportationValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'transportation_id' => ['required', 'exists:transportations,id'],
        ]);
    }
}
