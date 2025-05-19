<?php

declare(strict_types=1);

use App\Http\Services\NotificationServices;
use App\Models\V1\Notification;
use App\Models\V1\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->services = new NotificationServices();
    $this->user = User::factory()->create();
});

describe('Notification Service', function () {

    it('retrive all Notifications for user', function () {
        Notification::factory()->create([
            'belongTo_type' => $this->user::class,
            'belongTo_id' => $this->user->id,
        ]);
        $res = $this->services->all($this->user);
        expect($res)->toBeInstanceOf(Collection::class)->toHaveCount(1);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'belongTo_type' => $this->user::class,
            'belongTo_id' => $this->user->id,
        ]);
    });

    it('retrive all Notifications for valid badge', function () {
        Notification::factory()->create([
            'belongTo_type' => $this->user->badge::class,
            'belongTo_id' => $this->user->badge->id,
        ]);
        $res = $this->services->all($this->user->badge);
        expect($res)->toBeInstanceOf(Collection::class)->toHaveCount(1);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'belongTo_type' => $this->user->badge::class,
            'belongTo_id' => $this->user->badge->id,
        ]);
    });

    it('can find a notification', function () {
        $notification = Notification::factory()->create();

        expect($this->services->find($notification->id))->toBeInstanceOf(Notification::class);
    });

    it('can mark a notification as viewed', function () {
        $notification = Notification::factory()->create(['status' => 0]);
        expect($this->services->viewed($notification->id))->toBeTrue();
        expect($notification->refresh()->status)->toBe(1);
    });

    it('can mark multiple notifications as viewed', function () {
        $notifications = Notification::factory()->count(3)->create(['status' => 0]);
        $ids = $notifications->pluck('id')->toArray();
        expect($this->services->multipleViewed($ids))->toBeTrue();
        expect(Notification::whereIn('id', $ids)->get()->pluck('status')->unique()->first())->toBe(1);
    });

    it('fails to retrive Notifications with invalid badeg', function () {
        $this->services->all((object) []);
    })->throws(Exception::class);

    it('fails to retrive Notifications for badge when not found', function () {
        $this->services->all($this->user);
    })->throws(NotFoundHttpException::class);

    it('throws an exception when finding a non-existing notification', function () {
        expect(fn () => $this->services->find(99999))->toThrow(NotFoundHttpException::class);
    });

    it('returns false when trying to mark a non-existing notification as viewed', function () {
        expect(fn () => $this->services->viewed(99999))->toThrow(NotFoundHttpException::class);
    });

    it('handles empty array input for multipleViewed', function () {
        expect(fn () => $this->services->multipleViewed([]))->toThrow(Exception::class);
    });

    it('handles mixed valid and invalid IDs for multipleViewed', function () {
        $notifications = Notification::factory()->count(2)->create(['status' => 0]);
        $validIds = $notifications->pluck('id')->toArray();
        $invalidIds = [99999, 88888];
        $mixedIds = array_merge($validIds, $invalidIds);

        expect($this->services->multipleViewed($mixedIds))->toBeTrue();
        expect(Notification::whereIn('id', $validIds)->get()->pluck('status')->unique()->first())->toBe(1);
    });
});
