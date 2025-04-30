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
        if (! method_exists($belongTo, 'location')) {
            $this->NotFound(false, 'Location method');
        }

        return $belongTo->location()->create([
            'belongTo_type' => $belongTo::class,
            'long' => round((float) $data['cords']['long'], 8),
            'lat' => round((float) $data['cords']['lat'], 8),
        ]);
    }

    public function edit(object $belongTo, array $data): bool|Location
    {
        $this->Required($belongTo, __('main.belongTo'));
        $this->Required($data, __('main.data'));
        if (! method_exists($belongTo, 'location')) {
            $this->NotFound(false, 'Location method');
        }
        if ($belongTo->location()->exists()) {
            return $this->update($belongTo, $data);
        }

        return $this->create($belongTo, $data);

    }

    public function update(object $belongTo, array $data): bool|Location
    {
        return $belongTo->location->update([
            'long' => round((float) $data['cords']['long'], 8),
            'lat' => round((float) $data['cords']['lat'], 8),
        ]);
    }
}
