<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;

class LocationActions
{
    use ExceptionHandler;

    public function create(object $belongTo, array $data)
    {
        $this->Required($belongTo, __('main.belongTo'));
        $this->Required($data, __('main.data'));

        return $belongTo->location()->create([
            'belongTo_type' => $belongTo::class,
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }

    public function edit(object $belongTo, array $data)
    {
        $this->Required($belongTo, __('main.belongTo'));
        $this->Required($data, __('main.data'));

        return $belongTo->location()->update([
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }
}
