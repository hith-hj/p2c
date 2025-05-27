<?php

declare(strict_types=1);

use App\Models\V1\Transportation;
use App\Models\V1\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->api('carrier');
    $this->url = 'api/v1/transportation';
    $this->seed();
});

describe('Transportation controller', function () {

    it('returns all transportations', function () {
        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(200)
            ->and($res->json('payload.transportations'))->not->toBeEmpty();
    });

    it('fails to returns transportations when there is none', function () {
        Transportation::truncate();
        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(404);
    });

    it('finds a specific transportation', function () {
        $trans = Transportation::first();
        $res = $this->getJson("$this->url/find?transportation_id=$trans->id");

        expect($res->status())->toBe(200)
            ->and($res->json('payload.transportation.id'))->toBe($trans->id);
    });

    it('fails to finds a transportation with invalid id', function () {
        $res = $this->getJson("$this->url/find");
        expect($res->status())->toBe(422);
    });
});
