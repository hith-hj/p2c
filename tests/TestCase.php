<?php

declare(strict_types=1);

namespace Tests;

use App\Models\V1\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    public $user = null;

    public function api($as){
        if($this->user === null || $this->user->role !== $as){
            $this->user($as);
        }
        $token = JWTAuth::fromUser($this->user);
        $this->withHeaders(['Authorization' => "Bearer $token"]);
        return $this;
    }

    private function user($type){
        return $this->user = User::factory()->create(['role' => $type]);
    }

}
