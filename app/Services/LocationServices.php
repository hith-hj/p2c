<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\V1\Location;

final class LocationServices
{
    public function create(object $locatable, array $data): Location
    {
        Required($data, 'data');
        Truthy(! method_exists($locatable, 'location'), 'missing location method');
        $data = $this->checkAndCastData($data, [
            'cords' => 'array',
            'cords.long' => 'float',
            'cords.lat' => 'float',
        ]);

        return $locatable->location()->create([
            'long' => round($data['cords']['long'], 8),
            'lat' => round($data['cords']['lat'], 8),
        ]);
    }

    public function edit(object $locatable, array $data): bool|Location
    {
        Required($data, 'data');
        Truthy(! method_exists($locatable, 'location'), 'missing location method');
        if ($locatable->location()->exists()) {
            return $this->update($locatable, $data);
        }

        return $this->create($locatable, $data);
    }

    public function update(object $locatable, array $data): bool
    {
        $data = $this->checkAndCastData($data, [
            'cords' => 'array',
            'cords.long' => 'float',
            'cords.lat' => 'float',
        ]);

        return $locatable->location->update([
            'long' => round($data['cords']['long'], 8),
            'lat' => round($data['cords']['lat'], 8),
        ]);
    }

    private function checkAndCastData(array $data, $requiredFields = []): array
    {
        Truthy(empty($data), 'data is empty');
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
        Falsy(empty($missing), 'fields missing: '.implode(', ', $missing));

        return $data;
    }
}
