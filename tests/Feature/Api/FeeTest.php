<?php

declare(strict_types=1);

use App\Enums\FeeTypes;
use App\Models\V1\Fee;

beforeEach(function () {
    $this->api('carrier');
    $this->url = 'api/v1/fee';
    $this->data = [
        'subject_id' => rand(1, 5),
        'subject_type' => array_rand(['App\Models\V1\Order' => 1]),
        'type' => FeeTypes::normal->value,
        'amount' => '100',
        'delay_fee' => '20',
        'due_date' => now(),
        'status' => 0,
    ];
    $this->carrierData = array_merge($this->data, ['belongTo_type' => get_class($this->user->badge)]);
});
describe('Fee controller', function () {

    it('returns all fees', function () {
        $fees = $this->user->badge->fees()->create($this->carrierData);
        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(200)
            ->and($res->json('payload.fees'))->not->toBeEmpty();
    });

    it('fails to returns fees when there is none', function () {
        $res = $this->getJson("$this->url/all");
        expect($res->status())->toBe(404);
    });

    it('finds a specific Fee', function () {
        $this->user->badge->fees()->create($this->carrierData);
        $fee = Fee::first();
        $res = $this->getJson("$this->url/find?fee_id=$fee->id");

        expect($res->status())->toBe(200)
            ->and($res->json('payload.fee.id'))->toBe($fee->id);
    });

    it('fails to finds a fee with invalid id', function () {
        $res = $this->getJson("$this->url/find");
        expect($res->status())->toBe(422);
    });
});
