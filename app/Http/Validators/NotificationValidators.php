<?php

declare(strict_types=1);

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class NotificationValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'notification_id' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }

    public static function viewed($data)
    {
        return Validator::make($data, [
            'notification_id' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }

    public static function multibleViewed($data)
    {
        return Validator::make($data, [
            'notifications' => ['required', 'array', 'min:1'],
            'notifications.*' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }

    public static function delete($data)
    {
        return Validator::make($data, [
            'notification_id' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }

    public static function multibleDelete($data)
    {
        return Validator::make($data, [
            'notifications' => ['required', 'array', 'min:1'],
            'notifications.*' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }
}
