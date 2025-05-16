<?php

declare(strict_types=1);

use App\Enums\FeeTypes;
use App\Http\Services\FeeServices;
use App\Models\V1\Fee;
use App\Models\V1\User;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->feeServices = new FeeServices();
    $this->carrier = User::factory()->create(['role' => 'carrier']);
    $this->producer = User::factory()->create(['role' => 'producer']);
    $this->data = [
        'subject_id' => rand(1, 5),
        'subject_type' => array_rand(['App\Models\V1\Order' => 1]),
        'type' => FeeTypes::normal->value,
        'amount' => '100',
        'delay_fee' => '20',
        'due_date' => now(),
        'status' => 0,
    ];
    $this->carrierData = array_merge($this->data, ['belongTo_type' => get_class($this->carrier->badge)]);
    $this->producerData = array_merge($this->data, ['belongTo_type' => get_class($this->producer->badge)]);
});

describe('Fee Services', function () {
    it('retrieves all fees for carrier', function () {
        $this->carrier->badge->fees()->create($this->carrierData);
        $result = $this->feeServices->get($this->carrier->badge);
        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount(1);
    });

    it('fails to retrieves fees for carrier if not found', function () {
        $this->feeServices->get($this->carrier->badge);
    })->throws(Exception::class);

    it('retrieves all fees for producer', function () {
        $this->carrier->badge->fees()->create($this->producerData);
        $result = $this->feeServices->get($this->producer->badge);
        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount(1);
    });

    it('fails to retrieves fees for producer if not found', function () {
        $this->feeServices->get($this->producer->badge);
    })->throws(Exception::class);

    it('retrieves fee by id ', function () {
        $fee = Fee::create(['belongTo_id' => 1, ...$this->producerData]);
        $res = $this->feeServices->find($fee->id);
        expect($res)->toBeInstanceOf(Fee::class)
            ->and($res->id)->toBe($fee->id);
    });

    it('fails to retrieves fee by invalid id', function () {
        $this->feeServices->find(100);
    })->throws(Exception::class);
});
