<?php

declare(strict_types=1);

use App\Models\V1\Carrier;
use App\Models\V1\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'carrier']);
    $this->user->badge->update(['is_valid' => 1]);
    $token = JWTAuth::fromUser($this->user);
    $this->withHeaders(['Authorization' => "Bearer $token"]);
    $this->url = 'api/v1/carrier';
    $this->seed();
});

describe('Carrier Controller', function () {
    it('retrieves all carriers', function () {
        User::factory()->count(3)->create(['role' => 'carrier']);

        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(200);
        expect($res->json('payload.carriers'))->toBeArray()->not->toBeEmpty();
    });

    it('retrieves authenticated user carrier', function () {
        $res = $this->getJson($this->url);
        expect($res->status())->toBe(200);
        expect($res->json('payload.carrier'))->toBeArray()->not->toBeEmpty();
    });

    it('finds a specific carrier', function () {
        $user = User::factory()->create(['role' => 'carrier']);
        expect($user->badge)->not->toBeNull();
        $id = $user->badge->id;
        $res = $this->getJson("$this->url/find?carrier_id=$id");
        expect($res->status())->toBe(200);
        expect($res->json('payload.carrier'))->toBeArray()->not->toBeEmpty();
    });

    it('fails to find a carrier with an invalid ID', function () {
        $res = $this->getJson("$this->url/find?carrier_id=999");

        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->toBeArray()->not->toBeEmpty();
    });

    it('creates a new carrier', function () {
        $user = User::create([
            'email' => 'test@test.com',
            'phone' => '0911112222',
            'password' => 'password',
            'role' => 'carrier',
            'firebase_token' => 'some-token-here',
            'verified_at' => now(),
        ]);
        $token = JWTAuth::fromUser($user);
        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ]);
        $res = $this->postJson("$this->url/create", [
            'first_name' => 'Test',
            'last_name' => 'carrier',
            'transportation_id' => 1,
        ]);
        expect($res->status())->toBe(200);
        expect($res->json('payload.carrier.name'))->toContain('Test');
    });

    it('prevent user to creates a new carrier if exists', function () {
        $res = $this->postJson("$this->url/create", [
            'first_name' => 'Test',
            'last_name' => 'carrier',
            'transportation_id' => 1,
        ]);
        expect($res->status())->toBe(400);
        expect($res->json('success'))->toBe(false);
    });

    it('allow carrier to create details if not exists', function () {
        $data = [
            'plate_number' => '0909099',
            'brand' => 'Kia',
            'model' => '2500',
            'year' => '2010',
            'color' => 'red',
        ];
        $res = $this->postJson("$this->url/createDetails", $data);
        expect($res->status())->toBe(200);
        expect($res->json('payload.carrier'))->not->toBeNull();
        expect($res->json('payload.carrier.details'))->not->toBeNull();
        expect($res->json('payload.carrier.details.plate_number'))->toBe('0909099');
    });

    it('prevent carrier to create details if exists', function () {
        $data = [
            'plate_number' => '3387622',
            'brand' => 'Kia',
            'model' => '2500',
            'year' => '2010',
            'color' => 'red',
        ];
        $base = $this->postJson("$this->url/createDetails", $data);
        expect($base->status())->toBe(200);
        expect($base->json('payload.carrier'))->not->toBeNull();
        $res = $this->postJson("$this->url/createDetails", $data);
        expect($res->status())->toBe(422);
        expect($res->json('success'))->toBe(false);
    });

    it('updates an existing carrier', function () {
        $data = ['first_name' => 'Edited', 'last_name' => 'Carrier'];
        $res = $this->patchJson("$this->url/update", $data);
        expect($res->status())->toBe(200);
        expect($this->user->badge->fresh()->first_name)->toBe('Edited');
    });

    it('prevent carrier update details with empty data', function () {
        $data = [];
        $res = $this->patchJson("$this->url/update", $data);
        expect($res->status())->toBe(400);
    });

    it('updates an existing carrier transportation and delete old details', function () {
        $data = ['transportation_id' => 2];
        $res = $this->patchJson("$this->url/update", $data);
        expect($res->status())->toBe(200);
        $user = $this->user->badge->fresh();
        expect($user->transportation_id)->toBe(2);
        expect($user->details)->toBeNull();
    });

    it('invalidate carrier when update transportation details', function () {
        $user = $this->user->badge;
        expect($user->is_valid)->toBe(1);
        $data = ['transportation_id' => 2];
        $res = $this->patchJson("$this->url/update", $data);
        expect($res->status())->toBe(200);
        $user = $user->fresh();
        expect($user->is_valid)->toBe(0);
    });

    it('deletes a carrier', function () {
        $res = $this->deleteJson("$this->url/delete");
        expect($res->status())->toBe(200);
        expect(Carrier::find($this->user->id))->toBeNull();
    });

    it('allow carrier to set location', function () {
        $location = [
            'cords' => [
                'long' => '32.000000',
                'lat' => '33.000000',
            ],
        ];
        $user = $this->user->badge;
        expect($user->location)->not->toBeNull();
        $res = $this->postJson("$this->url/setLocation", $location);
        expect($res->status())->toBe(200);
        $user = $user->fresh();
        expect($user->location)->not->toBeNull();
        expect($user->location->long)->toBe(32.000000);
        expect($user->location->lat)->toBe(33.000000);
    });

    it('make sure that location is rounded to 8 digits after point', function () {
        $location = [
            'cords' => [
                'long' => '32.998765432100',
                'lat' => '33.998765432100',
            ],
        ];
        $user = $this->user->badge;
        expect($user->location)->not->toBeNull();
        $res = $this->postJson("$this->url/setLocation", $location);
        expect($res->status())->toBe(200);
        $user = $user->fresh();
        expect($user->location)->not->toBeNull();
        expect($user->location->long)->toBe(32.99876543);
        expect($user->location->lat)->toBe(33.99876543);
    });
});
