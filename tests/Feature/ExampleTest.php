<?php

declare(strict_types=1);

it('create user successfuly ', function () {
    $this->postJson(url('http://127.0.0.1:8000/api/v1/auth/register'), [
        'email' => 'api@example.com',
        'phone' => '0922222222',
        'password' => 'password',
        'role' => 'carrier',
    ])->assertStatus(200);
});
