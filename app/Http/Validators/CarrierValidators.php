<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class CarrierValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'carrier_id' => ['required', 'exists:carriers,id'],
        ]);
    }

    public static function create($data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'min:4', 'max:20'],
            'last_name' => ['required', 'string', 'min:4', 'max:20'],
            'transportation_id' => ['required', 'exists:transportations,id'],
        ]);
    }

    public static function createDetails($data)
    {
        return Validator::make($data, [
            'plate_number' => ['required', 'numeric', 'unique:carrier_details,plate_number'],
            'brand' => ['required', 'string'],
            'model' => ['required', 'string'],
            'year' => ['required', 'date_format:Y'],
            'color' => ['required', 'string'],
        ]);
    }

    public static function createImages($data)
    {
        return Validator::make($data, [
            'images' => ['required', 'array', 'size:5'],
            'images.*' => ['required', 'image', 'max:2048'],
        ]);
    }

    public static function update($data)
    {
        return Validator::make($data, [
            'first_name' => ['sometimes', 'string', 'min:4', 'max:20'],
            'last_name' => ['sometimes', 'string', 'min:4', 'max:20'],
            'transportation_id' => ['sometimes', 'exists:transportations,id'],
        ]);
    }

    public static function setLocation($data)
    {
        return Validator::make($data, [
            'cords' => ['required', 'array', 'size:2'],
            'cords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'cords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
    }
}
