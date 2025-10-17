<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class AuthValidators
{
    public static function register(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', Rule::in(['producer', 'carrier'])],
            'firebase_token' => ['required'],
        ]);
    }

    public static function verify($data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
        ]);
    }

    public static function login($data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    public static function forgetPassword($data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
        ]);
    }

    public static function resetPassword($data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public static function resendCode($data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
        ]);
    }

    public static function changePAssword($data)
    {
        return Validator::make($data, [
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
