<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\V1\Location;
use App\Traits\ExceptionHandler;

class LocationServices
{
    use ExceptionHandler;

    /**
     * create location for the given object.
     * $belongTo is locatable object.
     *
     * $data have item which is
     * an array containing long and lat cords
     **/
    public function create(object $belongTo, array $data): Location
    {
        $this->Truthy(empty($data), 'data is required');
        $this->Truthy(! method_exists($belongTo, 'location'), 'missing location method');

        return $belongTo->location()->create([
            'belongTo_type' => $belongTo::class,
            'long' => round((float) $data['cords']['long'], 8),
            'lat' => round((float) $data['cords']['lat'], 8),
        ]);
    }

    public function edit(object $belongTo, array $data): bool|Location
    {
        $this->Truthy(empty($data), 'data required');
        $this->Truthy(! method_exists($belongTo, 'location'), 'missing location method');

        if ($belongTo->location()->exists()) {
            return $this->update($belongTo, $data);
        }

        return $this->create($belongTo, $data);
    }

    public function update(object $belongTo, array $data): bool
    {
        return $belongTo->location->update([
            'long' => round((float) $data['cords']['long'], 8),
            'lat' => round((float) $data['cords']['lat'], 8),
        ]);
    }
}
