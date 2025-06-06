<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\V1\Transportation;
use App\Traits\ExceptionHandler;
use Illuminate\Support\Collection;

final class TransportationServices
{
    use ExceptionHandler;

    public function all(): Collection
    {
        $transportaions = Transportation::all();
        $this->NotFound($transportaions, 'transportations');

        return $transportaions;
    }

    public function find(int $id): Transportation
    {
        $transportation = Transportation::find($id);
        $this->NotFound($transportation, 'transportation');

        return $transportation;
    }

    public function getMatchedTransportation(int $weight): Transportation
    {
        $maxCapacity = Transportation::max('capacity');
        // todo : get transportation based on the dimension of object
        // volume = width * height* length (cubic volume)
        throw_if($weight > $maxCapacity, "Max Capacity Currently is $maxCapacity");

        return Transportation::orderBy('capacity')
            ->where('capacity', '>=', $weight)
            ->first();

    }
}
