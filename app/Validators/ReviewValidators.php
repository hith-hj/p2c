<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class ReviewValidators
{
    public static function create($data)
    {
        return Validator::make($data, [
            'belongTo_id' => ['required', 'numeric'],
            'belongTo_type' => ['required', 'string'],
            'content' => ['nullable', 'string', 'max:700'],
            'rate' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);
    }
}
