<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\V1\Notification;
use Illuminate\Support\Collection;

final class NotificationServices
{
    public function all(object $user): Collection
    {
        Truthy(! method_exists($user, 'notifications'), 'missing notifications()');
        $notis = $user->notifications;
        if (method_exists($user, 'badge') && method_exists($user->badge, 'notifications')) {
            $notis = $notis->concat($user->badge->notifications);
        }
        NotFound($notis, 'notifications');

        return $notis->sortByDesc('created_at');
    }

    public function find(int $id): Notification
    {
        Required($id, 'Id');
        $noti = Notification::find($id);
        NotFound($noti, 'Notification');

        return $noti;
    }

    public function view(array $ids): bool|int
    {
        Required($ids, 'Id');

        return Notification::whereIn('id', $ids)->update(['status' => 1]);
    }

    public function delete(Notification $notification): bool|int
    {
        NotFound($notification, 'notification');

        return $notification->delete();
    }

    public function clear(object $object)
    {
        if (method_exists($object, 'notifications')) {
            $object->notifications()->delete();
        }

        return true;
    }
}
