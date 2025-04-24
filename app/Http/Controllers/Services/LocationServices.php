<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use App\ExceptionHandler;
use App\Models\V1\Location;

class LocationServices
{
    use ExceptionHandler;

    public function create(object $belongTo, array $data): Location
    {
        $this->Required($belongTo, __('main.belongTo'));
        $this->Required($data, __('main.data'));

        return $belongTo->location()->create([
            'belongTo_type' => $belongTo::class,
            'long' => round($data['coords']['long'],8),
            'lat' => round($data['coords']['lat'],8),
        ]);
    }

    public function edit(object $belongTo, array $data): Location
    {
        $this->Required($belongTo, __('main.belongTo'));
        $this->Required($data, __('main.data'));

        return $belongTo->location()->update([
            'long' => round($data['coords']['long'],8),
            'lat' => round($data['coords']['lat'],8),
        ]);
    }
}
