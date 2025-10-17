<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\V1\Transportation;
use Illuminate\Support\Collection;

final class TransportationServices
{
    public function all(): Collection
    {
        $transportaions = Transportation::all();
        NotFound($transportaions, 'transportations');

        return $transportaions;
    }

    public function find(int $id): Transportation
    {
        $transportation = Transportation::find($id);
        NotFound($transportation, 'transportation');

        return $transportation;
    }

    public function getMatchedTransportation(int $weight): Transportation
    {
        $maxCapacity = Transportation::max('capacity');
        // todo : get transportation based on the dimension of object
        // volume = width * height* length (cubic volume)
        Truthy($weight > $maxCapacity, "Max Capacity Currently is $maxCapacity");

        return Transportation::orderBy('capacity')
            ->where('capacity', '>=', $weight)
            ->first();

    }
}
