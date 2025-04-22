<?php

declare(strict_types=1);
use App\Models\V1\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registers_a_user', function () {
    $data = [
        'email' => 'test@example.com',
        'phone' => '09'.rand(10000000, 99999999),
        'password' => 'password',
        'password_confirmation' => 'password',
        'account_type' => 'producer',
        'firebase_token' => 'some-firebase-token',
    ];
    $res = $this->postJson(route('register'), $data);
    expect($res->status())->toBe(201);
    expect($res->json())->toHaveKey('success', true);
});

test('fails_to_registers_a_user', function () {
    $data = [
        'email' => 'test@.com',
        'phone' => '09'.rand(10000000, 99999999),
        'password' => 'passwor',
        'password_confirmation' => 'passworx',
        'account_type' => 'xxx',
        'firebase_token' => '',
    ];
    $res = $this->postJson(route('register'), $data);
    expect($res->status())->toBe(400);
    expect($res->json())->toHaveKey('success', false);
});

test('verifies_user_successfully', function () {
    $data = [
        'email' => 'test@example.com',
        'phone' => '0987654321',
        'password' => bcrypt('password'),
        'role' => 'producer',
        'firebase_token' => 'some-firebase-token',
    ];
    $user = User::create($data);
    $user->createCode('verification');
    $code = $user->code('verification')->code;
    expect($code)->not->toBeNull();
    expect($code)->toBeInt();

    $res = $this->postJson(route('verify'), [
        'phone' => $user->phone,
        'code' => $code,
    ]);

    expect($res->status())->toBe(200);
    expect($res->json())->toHaveKey('message', __('main.verified'));
});

test('fails_to_verify_with_incorrect_code', function () {
    $data = [
        'email' => 'test'.rand(100, 999).'@example.com',
        'phone' => '09'.rand(10000000, 99999999),
        'password' => bcrypt('password'),
        'role' => 'producer',
        'firebase_token' => 'some-firebase-token',
    ];
    $user = User::create($data);
    $user->createCode('verification');
    $res = $this->postJson(route('verify'), [
        'phone' => $user->phone,
        'code' => 00000,
    ]);
    expect($res->status())->toBe(400);
    expect($res->json())->toHaveKey('payload.errors');
});
