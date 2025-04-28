<?php

declare(strict_types=1);
use App\Models\V1\User;

it('registers_a_user', function () {
    $data = [
        'email' => 'test@example.com',
        'phone' => '0912312312',
        'password' => 'password',
        'password_confirmation' => 'password',
        'account_type' => 'producer',
        'firebase_token' => 'some-firebase-token',
    ];
    $res = $this->postJson(route('register'), $data);
    expect($res->status())->toBe(201);
    expect($res->json('success'))->toBe(true);
});

it('fails_to_registers_a_user', function () {
    $data = [
        'email' => 'test@.com',
        'phone' => '0912312312',
        'password' => 'passwor',
        'password_confirmation' => 'passworx',
        'account_type' => 'xxx',
        'firebase_token' => '',
    ];
    $res = $this->postJson(route('register'), $data);
    expect($res->status())->toBe(400);
    expect($res->json('success'))->toBe(false);
});

it('verifies_user_successfully', function () {
    $user = User::factory()->create(['phone' => '0912345678']);
    $user->createCode('verification');
    $code = $user->code('verification')->code;
    expect($code)->not->toBeNull();
    expect($code)->toBeInt();

    $res = $this->postJson(route('verify'), [
        'phone' => $user->phone,
        'code' => $code,
    ]);
    expect($res->status())->toBe(200);
    expect($res->json('message'))->toBe(__('main.verified'));
});

it('fails_to_verify_with_incorrect_code', function () {
    $user = User::factory()->create();
    $user->createCode('verification');
    $res = $this->postJson(route('verify'), [
        'phone' => $user->phone,
        'code' => 00000,
    ]);
    expect($res->status())->toBe(400);
    expect($res->json('payload.errors'))->not->toBeNull();
});

it('allow_verified_user_login', function () {
    $user = User::factory()->create(['phone' => '0911112222']);
    $res = $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'password',
    ]);
    expect($res->status())->toBe(200);
    expect($res->json('payload'))->toHaveKeys(['user', 'token']);
});

it('prevent_unverified_user_login', function () {
    $user = User::factory()->create([
        'phone' => '0911112222',
        'verified_at' => null,
    ]);

    $res = $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'password',
    ]);

    expect($res->status())->toBe(401);
    expect($res->json('message'))->toBe(__('main.unverified'));
});

it('fails_to_login_with_invalid_credentials', function () {
    $res = $this->postJson('/api/v1/auth/login', [
        'phone' => '0912345678',
        'password' => 'wrongpassword',
    ]);
    expect($res->status())->toBe(400);
    expect($res->json('payload.errors'))->not->toBeNull();
});

it('fails_to_login_with_incorrect_credentials', function () {
    $user = User::factory()->create(['phone' => '0911112222']);
    $res = $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'wrongpassword',
    ]);
    expect($res->status())->toBe(400);
    expect($res->json('message'))->toBe(__('main.invalid credentials'));
});
