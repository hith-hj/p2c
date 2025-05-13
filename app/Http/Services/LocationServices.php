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
        $data = $this->checkAndCastData($data, [
            'cords' => 'array',
            'cords.long' => 'float',
            'cords.lat' => 'float',
        ]);

        return $belongTo->location()->create([
            'long' => round($data['cords']['long'], 8),
            'lat' => round($data['cords']['lat'], 8),
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
        $data = $this->checkAndCastData($data, [
            'cords' => 'array',
            'cords.long' => 'float',
            'cords.lat' => 'float',
        ]);

        return $belongTo->location->update([
            'long' => round($data['cords']['long'], 8),
            'lat' => round($data['cords']['lat'], 8),
        ]);
    }

    private function checkAndCastData(array $data, $requiredFields = []): array
    {
        $this->Truthy(empty($data), 'data is empty');
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = [];
        foreach ($requiredFields as $key => $value) {
            if (str_contains($key, '.')) {
                [$name, $sub] = explode('.', $key);
                if (! isset($data[$name][$sub])) {
                    $missing[] = $key;

                    continue;
                }
                settype($data[$name][$sub], $value);

                continue;
            }
            if (! isset($data[$key])) {
                $missing[] = $key;

                continue;
            }
            settype($data[$key], $value);
        }
        $this->Falsy(empty($missing), 'fields missing: '.implode(', ', $missing));

        return $data;
    }
}
