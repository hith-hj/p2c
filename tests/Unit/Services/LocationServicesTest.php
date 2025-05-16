<?php

declare(strict_types=1);

use App\Http\Services\LocationServices;
use App\Models\V1\Branch;
use App\Models\V1\Location;

beforeEach(function () {
    $this->locationServices = new LocationServices();
});

describe('Location Service', function () {

    it('creates a location', function () {
        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];
        $branch = Branch::factory()->create();
        expect($branch->location)->toBeNull();
        $location = $this->locationServices->create($branch, $data);
        expect($location)->toBeInstanceOf(Location::class);
        expect($branch->fresh()->location)->not->toBeNull();
        expect($branch->fresh()->location->long)->toBe(35.0);
    });

    it('fails to create location when badge missing location method', function () {
        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];
        $this->locationServices->create((object) [], $data);
    })->throws(Exception::class);

    it('edits a location by creating when no existing location', function () {
        $branch = Branch::factory()->create();
        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];

        $result = $this->locationServices->edit($branch, $data);

        expect($result)->toBeInstanceOf(Location::class);
        expect($branch->fresh()->location->long)->toBe(35.0);
    });

    it('fails to edit location when badge missing location method', function () {
        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];
        $this->locationServices->edit((object) [], $data);
    })->throws(Exception::class);

    it('updates a location when existing ', function () {
        $branch = Branch::factory()->create();
        $location = Location::factory()->make()->toArray();
        $branch->location()->create($location);
        $data = [
            'cords' => ['long' => 35.0, 'lat' => 40.0],
        ];
        expect($branch->location)->not->toBeNull();
        $result = $this->locationServices->edit($branch, $data);

        expect($result)->toBeTrue();
        expect($branch->fresh()->location->long)->toBe(35.0);
    });

    it('test updates function alone', function () {
        $branch = Branch::factory()->create();
        $location = Location::factory()->make()->toArray();
        $branch->location()->create($location);
        $data = ['cords' => ['long' => 35.0, 'lat' => 40.0]];
        $result = $this->locationServices->update($branch, $data);
        expect($result)->toBeTrue();
        expect($branch->fresh()->location->long)->toBe(35.0);
    });
});
