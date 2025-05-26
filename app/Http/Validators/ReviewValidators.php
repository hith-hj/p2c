<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class ReviewValidators
{
    public static function create($data)
    {
        return Validator::make($data, [
            'reviewed_id' => ['required', 'numeric'],
            'reviewed_type' => ['required', 'string'],
            'content' => ['nullable', 'string', 'max:700'],
            'rate' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);
    }
}
