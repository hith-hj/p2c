<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class BranchValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'branch_id' => ['sometimes', 'required', 'exists:branches,id'],
        ]);
    }

    public static function create($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
            'cords' => ['required', 'array', 'size:2'],
            'cords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'cords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
    }

    public static function update($data)
    {
        return Validator::make($data, [
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['sometimes', 'required', 'string', 'min:4', 'max:100'],
            'phone' => ['sometimes', 'required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:branches,phone'],
        ]);
    }

    public static function delete($data)
    {
        return Validator::make($data, [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);
    }

    public static function setDefault($data)
    {
        return Validator::make($data, [
            'branch_id' => ['required', 'exists:branches,id'],
        ]);
    }
}
