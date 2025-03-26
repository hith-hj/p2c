<?php

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
class LocationActions
{
    use ExceptionHandler;
    public function create($locatable, $data)
    {
        $this->Required($locatable,'Location');
        $this->Required($data,'Location Data');

        return $locatable->location()->create([
            'locatable_type' => get_class($locatable),
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }

    public function edit($locatable, $data)
    {
        $this->Required($locatable,'Location');
        $this->Required($data,'Location Data');

        return $locatable->location()->update([
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }

}
