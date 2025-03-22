<?php

namespace App;

use App\Models\V1\User;

trait FirebaseNotification
{
    public function notifyFCM(User $user) {}

    public function notifyEmail(User $user) {}
}
