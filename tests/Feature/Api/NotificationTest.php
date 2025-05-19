<?php

declare(strict_types=1);

use App\Models\V1\Notification;
use App\Models\V1\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'carrier']);
    $this->user->badge->update(['is_valid' => 1]);
    $token = JWTAuth::fromUser($this->user);
    $this->withHeaders(['Authorization' => "Bearer $token"]);
    $this->url = 'api/v1/notification';
    $this->seed();
});

describe('notification controller test', function () {

    it('fails to access the controller without valid badge', function () {
        $this->user->badge->update(['is_valid' => 0]);
        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(403);
    });

    it('returns all notifications for authenticated user', function () {
        Notification::factory()->for($this->user, 'reciver')->count(3)->create();
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
        $res = $this->postJson("$this->url/viewed", ['notification_id' => $notification->id]);
        expect($res->status())->toBe(200);
        expect($notification->refresh()->status)->toBe(1);
    });

    it('fails to mark a non-existing notification as viewed', function () {
        $res = $this->postJson("$this->url/viewed", ['notification_id' => 67876]);
        expect($res->status())->toBe(422);
    });

    it('updates multiple notifications successfully', function () {
        $notifications = Notification::factory()->count(3)->create(['status' => 0]);
        $ids = $notifications->pluck('id')->toArray();
        $res = $this->postJson("$this->url/multipleViewed", ['notifications' => $ids]);
        expect($res->getStatusCode())->toBe(200);
        expect(Notification::whereIn('id', $ids)->get()->pluck('status')->unique()->first())->toBe(1);
    });

    it('handles empty notification array gracefully', function () {
        $res = $this->postJson("$this->url/multipleViewed", ['notifications' => []]);
        expect($res->getStatusCode())->toBe(422);
    });
});
