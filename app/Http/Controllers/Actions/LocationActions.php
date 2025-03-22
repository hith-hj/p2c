<?php

namespace App\Http\Controllers\Actions;

class LocationActions
{
    public function create($locatable, $data)
    {
        $this->assertArgs($locatable, $data);

        return $locatable->location()->create([
            'locatable_type' => get_class($locatable),
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }

    public function edit($locatable, $data)
    {
        $this->assertArgs($locatable, $data);

        return $locatable->location()->update([
            'long' => $data['coords']['long'],
            'lat' => $data['coords']['lat'],
        ]);
    }

    private function assertArgs($locatable, $data)
    {
        if (! $locatable || ! $data) {
            throw new \Exception('Invalid Location Arguments');
        }
    }
}
