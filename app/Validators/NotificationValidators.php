<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class NotificationValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'notification_id' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }

    public static function view($data)
    {
        return Validator::make($data, [
            'notifications' => ['required', 'array', 'min:1'],
        ]);
    }

    public static function delete($data)
    {
        return Validator::make($data, [
            'notification_id' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }
}
