<?php

declare(strict_types=1);

namespace App;

use App\Models\V1\User;

trait FirebaseNotification
{
    public function notifyFCM(User $user) {}

    public function notifyEmail(User $user) {}
}
