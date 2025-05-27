<?php

declare(strict_types=1);

use App\Models\V1\Notification;

beforeEach(function () {
    $this->api('carrier');
    $this->url = 'api/v1/notification';
    $this->seed();
});

describe('notification controller test', function () {

    it('fails to access the controller while invalid', function () {
        $this->user->update(['verified_at' => null]);
        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(403);
    });

    it('returns all notifications for authenticated user', function () {
        Notification::factory()->for($this->user, 'belongTo')->count(3)->create();
        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(200);
        expect($res->json('payload.notifications'))->toHaveCount(3);
    });

    it('returns a single notification when found', function () {
        $notification = Notification::factory()->create();
        $res = $this->getJson("$this->url/find?notification_id=$notification->id");

        expect($res->status())->toBe(200);
        expect($res->json('payload.notification'))->not->toBeNull();
    });

    it('handles missing notification gracefully', function () {
        $res = $this->getJson("$this->url/find?notification_id=45456546");
        expect($res->status())->toBe(422);
    });

    it('marks a notification as viewed successfully', function () {
        $notification = Notification::factory()->create(['status' => 0]);
        $res = $this->postJson("$this->url/view", ['notifications' => [$notification->id]]);
        expect($res->status())->toBe(200);
        expect($notification->refresh()->status)->toBe(1);
    });

    it('fails to mark a invalid notification as viewed', function () {
        $res = $this->postJson("$this->url/view", ['notifications' => []]);
        expect($res->status())->toBe(422);
    });

    it('delete notification for user by id', function () {
        $noti = Notification::factory()->for($this->user, 'belongTo')->create(['status' => 0]);
        expect($this->user->notifications()->count())->toBe(1);
        $res = $this->postJson("$this->url/delete", ['notification_id' => $noti->id]);
        expect($res->status())->toBe(200)
            ->and($this->user->notifications()->count())->toBe(0);

    });

    it('fails to delete notification by id if not belong to user', function () {
        $noti = Notification::factory()->create(['status' => 0]);
        $res = $this->postJson("$this->url/delete", ['notification_id' => $noti->id]);
        expect($res->status())->toBe(403);
    });

    it('fails to delete notification with invalid id', function () {
        $res = $this->postJson("$this->url/delete", ['notifications' => []]);
        expect($res->status())->toBe(422);
    });
});
