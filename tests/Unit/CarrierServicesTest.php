<?php

declare(strict_types=1);

use App\Http\Services\CarrierServices;
use App\Models\V1\Carrier;
use App\Models\V1\CarrierDetails;
use App\Models\V1\Location;
use App\Models\V1\User;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->carrierServices = new CarrierServices();
});

describe('Carrier Services', function () {
    it('retrieves all carriers', function () {
        $count = 4;
        Carrier::factory($count)->create();
        $result = $this->carrierServices->all();

        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount($count);
    });

    it('throws exception if carriers not found', function () {
        $result = $this->carrierServices->all();
        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount(2);
    })->throws(Exception::class, 'Not Found');

    it('paginates carriers', function () {
        $count = 4;
        Carrier::factory($count)->create();
        $mockRequest = Mockery::mock('Illuminate\Http\Request');
        $mockRequest->shouldReceive('filled')->with('perPage')->andReturnFalse();

        $result = $this->carrierServices->paginate($mockRequest, $count);

        expect($result)->toBeInstanceOf('Illuminate\Pagination\LengthAwarePaginator')->toHaveCount($count);
    });

    it('throws exception if carriers not found when paginates', function () {
        $count = 4;
        $mockRequest = Mockery::mock('Illuminate\Http\Request');
        $mockRequest->shouldReceive('filled')->with('perPage')->andReturnFalse();
        $this->carrierServices->paginate($mockRequest, $count);
    })->throws(Exception::class, 'Not Found');

    it('retrieves carrier by ID', function () {
        $carrier = Carrier::factory()->createOne();
        $result = $this->carrierServices->find($carrier->id);

        expect($result)->toBeInstanceOf(Carrier::class)->id->toBe($carrier->id);
    });

    it('throws exception if carrier not found by id', function () {
        $this->carrierServices->find(999);
    })->throws(Exception::class, 'Not Found');

    it('creates a new carrier', function () {
        $user = User::factory()->create(['role' => 'carrier']);
        $user->badge()->delete();
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'transportation_id' => 1,
        ];

        $result = $this->carrierServices->create($user, $data);

        expect($result)->toBeInstanceOf(Carrier::class)->first_name->toBe('John');
    });

    it('faild to creates a new carrier with missing info', function () {
        $user = User::factory()->create(['role' => 'carrier']);
        $user->badge()->delete();
        $data = [
            'first_name' => 'John',
            // 'last_name' => 'Doe',
            'transportation_id' => 1,
        ];
        $this->carrierServices->create($user, $data);
    })->throws(Exception::class, 'Undefined');

    it('creates carrier details', function () {
        $carrier = Carrier::factory()->createOne();
        $data = [
            'plate_number' => 'ABC123',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => '2022',
            'color' => 'Blue',
        ];
        $result = $this->carrierServices->createDetails($carrier, $data);
        expect($result)->toBeInstanceOf(CarrierDetails::class)->plate_number->toBe('ABC123');
    });

    it('faild to creates a new carrier details with missing info', function () {
        $carrier = Carrier::factory()->createOne();
        $data = [
            'plate_number' => 'ABC123',
            // 'brand' => 'Toyota',
            // 'model' => 'Corolla',
            'year' => '2022',
            'color' => 'Blue',
        ];
        $this->carrierServices->createDetails($carrier, $data);
    })->throws(Exception::class, 'Undefined');

    it('updates an existing carrier', function () {
        $carrier = Carrier::factory()->createOne();
        $data = ['first_name' => 'Jane', 'last_name' => 'Smith'];
        $result = $this->carrierServices->update($carrier, $data);
        expect($result)->toBeTrue();
    });

    it('deletes a carrier', function () {
        $carrier = Carrier::factory()->createOne();
        $result = $this->carrierServices->delete($carrier);
        expect($result)->toBeTrue();
    });

    it('fail to deletes a carrier with invalid carrier', function () {
        $this->carrierServices->delete(999);
    })->throws(TypeError::class);

    it('sets carrier location', function () {
        $carrier = Carrier::factory()->createOne();
        $data = [
            'cords' => ['long' => 50.0, 'lat' => 60.0],
        ];
        $result = $this->carrierServices->setLocation($carrier, $data);
        expect($result)->toBeInstanceOf(Location::class);
    });

    it('fail to set carrier location with invalid data', function () {
        $carrier = Carrier::factory()->createOne();
        $data = [
            'cords' => ['long' => 50.0],
        ];
        $this->carrierServices->setLocation($carrier, $data);
    })->throws(Exception::class, 'Undefined');
});
