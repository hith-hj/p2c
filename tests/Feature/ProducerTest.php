<?php

declare(strict_types=1);

use App\Models\V1\Producer;
use App\Models\V1\User;
use Tymon\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $user = User::factory()->create(['role' => 'producer']);
    $token = JWTAuth::fromUser($user);
    $this->user = $user;
    $this->actingAs($user)->withHeaders([
        'Authorization' => "Bearer $token",
    ]);
    $this->url = 'api/v1/producer';
});

describe('ProducerController', function () {
    it('retrieves all producers', function () {
        User::factory()->count(3)->create(['role' => 'producer']);

        $res = $this->getJson("$this->url/all");

        $res->assertStatus(200);
        expect($res->json('payload.producers'))->toBeArray()->not->toBeEmpty();
    });

    it('retrieves authenticated user producer', function () {
        $res = $this->getJson($this->url);
        $res->assertStatus(200);
        expect($res->json('payload.producer'))->toBeArray()->not->toBeEmpty();
    });

    it('finds a specific producer', function () {
        $user = User::factory()->create(['role' => 'producer']);
        expect($user->badge)->not->toBeNull();
        $id = $user->badge->id;
        $res = $this->getJson("$this->url/find?producer_id=$id");
        $res->assertStatus(200);
        expect($res->json('payload.producer'))->toBeArray()->not->toBeEmpty();
    });

    it('fails to find a producer with an invalid ID', function () {
        $res = $this->getJson("$this->url/find?producer_id=999");

        $res->assertStatus(400);
        expect($res->json('payload.errors'))->toBeArray()->not->toBeEmpty();
    });

    it('creates a new producer', function () {
        $user = User::create([
            'email' => 'test@test.com',
            'phone' => '0911112222',
            'password' => 'password',
            'role' => 'producer',
            'firebase_token' => 'some-token-here',
            'verified_at' => now(),
        ]);
        $token = JWTAuth::fromUser($user);
        $this->actingAs($user)->withHeaders([
            'Authorization' => "Bearer $token",
        ]);
        $res = $this->postJson("$this->url/create", [
            'brand' => 'Test Brand',
            'coords' => ['long' => 10.5, 'lat' => 20.3],
        ]);
        $res->assertStatus(200);
        expect($res->json('payload.producer.brand'))->toBe('Test Brand');
    });

    it('updates an existing producer', function () {
        $res = $this->patchJson("$this->url/update", ['brand' => 'New Brand']);

        $res->assertStatus(200);
        expect($this->user->badge->brand)->toBe('New Brand');
    });

    it('deletes a producer', function () {
        $res = $this->deleteJson("$this->url/delete");
        $res->assertStatus(200);
        expect(Producer::find($this->user->id))->toBeNull();
    });
});
