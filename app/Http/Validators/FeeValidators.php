<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class FeeValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'fee_id' => ['required', 'exists:fees,id'],
        ]);
    }
}
