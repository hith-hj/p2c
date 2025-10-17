<?php

declare(strict_types=1);

use App\Services\CustomerServices;
use App\Models\V1\Customer;

beforeEach(function () {
    $this->customerServices = new CustomerServices();
    $this->customerData = [
        'phone' => '0911111111',
        'name' => 'name',
        'cords' => ['long' => 31.12121, 'lat' => 31.21212],
    ];
});

describe('Customer Services', function () {
    it('get customer when exists', function () {
        $customer = Customer::factory()->create([
            'phone' => '0911111111',
            'name' => 'dodge',
        ]);
        expect($customer->name)->toBe('dodge');
        $res = $this->customerServices->createIfNotExists($this->customerData);
        expect($res)->toBeInstanceOf(Customer::class);
        expect($res->phone)->toBe($this->customerData['phone']);
        expect($res->name)->toBe($customer->name);
    });

    it('create customer when not exists', function () {
        expect(Customer::all())->toHaveCount(0);
        $res = $this->customerServices->createIfNotExists($this->customerData);
        expect($res)->toBeInstanceOf(Customer::class);
        expect($res->phone)->toBe($this->customerData['phone']);
        expect(Customer::all())->toHaveCount(1);
    });

    it('fails to create customer with wrong data', function () {
        $this->customerServices->createIfNotExists([]);
    })->throws(Exception::class);

    it('find customer by id', function () {
        $customer = Customer::factory()->create();
        $res = $this->customerServices->find($customer->id);
        expect($res)->toBeInstanceOf(Customer::class);
        expect($res->id)->toBe($customer->id);
        expect($res->name)->toBe($customer->name);
    });

    it('fails to find customer by id if not exists', function () {
        $this->customerServices->find(9999);
    })->throws(Exception::class);

});
