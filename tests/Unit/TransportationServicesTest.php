<?php

declare(strict_types=1);

use App\Http\Controllers\Services\TransportationServices;
use App\Models\V1\Transportation;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->transportationServices = new TransportationServices();
});

describe('Transportation Services', function () {
    it('retrieves all transportations', function () {
        Transportation::factory(5)->create();
        $result = $this->transportationServices->all();
        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount(5);
    });

    it('throws an exception when transportations not found', function () {
        $this->transportationServices->all();
    })->throws(Exception::class, 'Not Found');

    it('retrieves a specific transportation by ID', function () {
        $trans = Transportation::factory()->createOne();
        $result = $this->transportationServices->find($trans->id);
        expect($result)->toBeInstanceOf(Transportation::class)->id->toBe($trans->id);
    });

    it('throws an exception when transportation not found', function () {
        $this->transportationServices->find(999);
    })->throws(Exception::class, 'Not Found');

    it('retrieves matched transportation based on weight', function () {
        Transportation::factory(5)->create();
        $result = $this->transportationServices->getMatchedTransportation(800);
        expect($result)->toBeInstanceOf(Transportation::class);
        expect($result->capacity)->toBeGreaterThanOrEqual(800);
    });

    it('throws an exception when weight exceeds maximum capacity', function () {
        Transportation::factory(5)->create();
        $this->transportationServices->getMatchedTransportation(15000);
    })->throws(Exception::class, 'Max Capacity');
});
