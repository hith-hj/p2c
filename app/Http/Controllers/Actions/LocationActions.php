<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;

class LocationActions
{
    use ExceptionHandler;

    public function create(object $locatable, array $data)
    {
        $this->Required($locatable, __('main.locatable'));
        $this->Required($data, __('main.data'));

        return $locatable->location()->create([
            'locatable_type' => $locatable::class,
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }

    public function edit(object $locatable, array $data)
    {
        $this->Required($locatable, __('main.locatable'));
        $this->Required($data, __('main.data'));

        return $locatable->location()->update([
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }
}
