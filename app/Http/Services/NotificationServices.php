<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\V1\Notification;
use App\Traits\ExceptionHandler;
use Illuminate\Support\Collection;

class NotificationServices
{
    use ExceptionHandler;

    public function all(object $user): Collection
    {
        $this->Truthy(! method_exists($user, 'notifications'), 'missing notifications()');
        $notis = $user->notifications;
        if (method_exists($user, 'badge') && method_exists($user->badge, 'notifications')) {
            $notis->concat($user->badge->notifications);
        }
        $this->NotFound($notis, 'notifications');
        $notis->sortBy('created_at');

        return $notis;
    }

    public function find(int $id): Notification
    {
        $this->Required($id, 'Id');
        $noti = Notification::find($id);
        $this->NotFound($noti, 'Notification');

        return $noti;
    }

    public function viewed(int $id): bool
    {
        $this->Required($id, 'Id');

        return $this->find($id)->update(['status' => 1]);
    }

    public function multipleViewed(array $ids): bool|int
    {
        $this->Required($ids, 'Ids');

        return Notification::whereIn('id', $ids)->update(['status' => 1]);
    }
}
