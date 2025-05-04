<?php

declare(strict_types=1);

use App\Http\Services\LocationServices;
use App\Models\V1\Branch;
use App\Models\V1\Location;
use Illuminate\Database\Eloquent\Relations\HasOne;

beforeEach(function () {
    $this->locationServices = new LocationServices();
    $this->belongTo = mock(Branch::class);
    $this->belongTo->shouldReceive('location')->andReturn(mock(HasOne::class));
});

describe('Location Service class', function () {

    it('creates a location', function () {
        $this->belongTo->location()
            ->shouldReceive('create')
            ->with([
                'belongTo_type' => get_class($this->belongTo),
                'long' => 35.00000000,
                'lat' => 40.00000000,
            ])
            ->andReturn(new Location());

        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];

        $location = $this->locationServices->create($this->belongTo, $data);

        expect($location)->toBeInstanceOf(Location::class);
    });

    it('fails to create location when badge missing location method', function () {
        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];
        $this->locationServices->create((object) [], $data);
    })->throws(Exception::class);

    it('edits a location by creating when no existing location', function () {
        $this->belongTo->location()
            ->shouldReceive('exists')
            ->andReturnFalse();
        $this->belongTo->location()
            ->shouldReceive('create')
            ->with([
                'belongTo_type' => get_class($this->belongTo),
                'long' => 35.00000000,
                'lat' => 40.00000000,
            ])
            ->andReturn(new Location());

        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];

        $result = $this->locationServices->edit($this->belongTo, $data);

        expect($result)->toBeInstanceOf(Location::class);
    });

    it('fails to edit location when badge missing location method', function () {
        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];
        $this->locationServices->edit((object) [], $data);
    })->throws(Exception::class);

    it('updates a location when existing ', function () {
        $this->belongTo->location()->shouldReceive('exists')->andReturnTrue();
        $mockLocation = mock(Location::class);
        $mockLocation->shouldReceive('update')
            ->with([
                'long' => 35.00000000,
                'lat' => 40.00000000,
            ])
            ->andReturnTrue();
        $this->belongTo->shouldReceive('getAttribute')->andReturn($mockLocation);

        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];

        $result = $this->locationServices->edit($this->belongTo, $data);

        expect($result)->toBeTrue();
    });

    it('test updates function alone', function () {
        $mockLocation = mock(Location::class);
        $mockLocation->shouldReceive('update')
            ->with([
                'long' => 35.00000000,
                'lat' => 40.00000000,
            ])
            ->andReturnTrue();
        $this->belongTo->shouldReceive('getAttribute')->andReturn($mockLocation);

        $data = ['cords' => ['long' => 35.0, 'lat' => 40.0]];
        $result = $this->locationServices->update($this->belongTo, $data);
        expect($result)->toBeTrue();
    });
});
