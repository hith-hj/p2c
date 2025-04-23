<?php

declare(strict_types=1);

use App\Models\V1\Producer;
use App\Models\V1\User;
use Tymon\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $user = User::factory()->create(['role' => 'carrier']);
    $token = JWTAuth::fromUser($user);
    $this->user = $user;
    $this->actingAs($user)->withHeaders([
        'Authorization' => "Bearer $token",
    ]);
    $this->url = 'api/v1/carrier';
});

describe('CarrierController', function () {
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

        expect($res->status())->toBe(400);
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
        $this->actingAs($user)->withHeaders([
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

    it('updates an existing carrier', function () {
        $data = ['first_name' => 'Edited', 'last_name' => 'Carrier'];
        $res = $this->patchJson("$this->url/update", $data);
        expect($res->status())->toBe(200);
        expect($this->user->badge->first_name)->toBe('Edited');
    });

    it('deletes a carrier', function () {
        $res = $this->deleteJson("$this->url/delete");
        expect($res->status())->toBe(200);
        expect(Producer::find($this->user->id))->toBeNull();
    });
});
