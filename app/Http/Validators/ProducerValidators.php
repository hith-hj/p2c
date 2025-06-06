<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

final class ProducerValidators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'producer_id' => ['required', 'exists:producers,id'],
        ]);
    }

    public static function create(array $data)
    {
        return Validator::make($data, [
            'brand' => ['required', 'string', 'min:4', 'max:50', 'unique:producers,brand'],
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'cords' => ['required', 'array', 'size:2'],
            'cords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'cords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
    }

    public static function update(array $data)
    {
        return Validator::make($data, [
            'brand' => ['required', 'string', 'min:4', 'max:50', 'unique:producers,brand'],
        ]);
    }
}
