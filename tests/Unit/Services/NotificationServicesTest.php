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

    it('fails to retrive Notifications with invalid badeg', function () {
        $this->services->all((object) []);
    })->throws(Exception::class);

    it('fails to retrive Notifications for badge when not found', function () {
        $this->services->all($this->user);
    })->throws(NotFoundHttpException::class);

    it('can find a notification by id', function () {
        $notification = Notification::factory()->create();

        expect($this->services->find($notification->id))->toBeInstanceOf(Notification::class);
    });

    it('can mark a notifications as viewed', function () {
        $notification = Notification::factory()->create(['status' => 0]);
        expect($this->services->view([$notification->id]))->toBeNumeric();
        expect($notification->refresh()->status)->toBe(1);
    });

    it('fails to mark a notifications as viewed with empty array', function () {
        expect(fn () => $this->services->view([]))->toThrow(Exception::class);
    });

    it('throws an exception when finding a non-existing notification', function () {
        expect(fn () => $this->services->find(99999))->toThrow(NotFoundHttpException::class);
    });

    it('can delete a notifications', function () {
        $notification = Notification::factory()->create(['status' => 0]);
        expect($this->services->delete($notification))->toBeTrue();
        expect($notification->fresh())->toBeNull();
    });

    it('fails to delete a notifications with invalid args', function () {
        expect(fn () => $this->services->delete([]))->toThrow(TypeError::class);
    });

    it('clear notification for user', function () {
        Notification::factory(5)->for($this->user, 'belongTo')->create(['status' => 0]);
        expect($this->user->notifications()->count())->toBe(5);
        expect($this->services->clear($this->user))->toBeTrue()
            ->and($this->user->notifications()->count())->toBe(0);
    });

    it('clear notification for user a badge', function () {
        Notification::factory(5)->for($this->user->badge, 'belongTo')->create(['status' => 0]);
        expect($this->user->badge->notifications()->count())->toBe(5);
        expect($this->services->clear($this->user->badge))->toBeTrue()
            ->and($this->user->badge->notifications()->count())->toBe(0);
    });
});
