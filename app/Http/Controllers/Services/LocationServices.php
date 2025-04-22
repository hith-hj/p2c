<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use App\ExceptionHandler;
use App\Models\V1\Location;
use Exception;

class LocationServices
{
    use ExceptionHandler;

    public function create(object $belongTo, array $data): Location|Exception
    {
        $this->Required($belongTo, __('main.belongTo'));
        $this->Required($data, __('main.data'));

        return $belongTo->location()->create([
            'belongTo_type' => $belongTo::class,
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }

    public function edit(object $belongTo, array $data): Location|Exception
    {
        $this->Required($belongTo, __('main.belongTo'));
        $this->Required($data, __('main.data'));

        return $belongTo->location()->update([
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }
}
