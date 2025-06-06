<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

final class FeeValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'fee_id' => ['required', 'exists:fees,id'],
        ]);
    }
}
