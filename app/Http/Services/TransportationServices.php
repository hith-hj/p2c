<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\ExceptionHandler;
use App\Models\V1\Transportation;
use Exception;
use Illuminate\Support\Collection;

class TransportationServices
{
    use ExceptionHandler;

    public function all(): Collection
    {
        $transportaions = Transportation::all();
        $this->NotFound($transportaions, __('main.transportations'));

        return $transportaions;
    }

    public function find(int $id): Transportation
    {
        $this->Required($id, __('main.carrier').' ID');
        $transportaion = Transportation::where('id', $id)->first();
        $this->NotFound($transportaion, __('main.transportation'));

        return $transportaion;
    }

    public function getMatchedTransportation(int $weight): Transportation
    {
        $maxCapacity = Transportation::max('capacity');
        // todo : get transportation based on the dimension of object
        // volume = width * height* length (cubic volume)
        if ($weight > $maxCapacity) {
            throw new Exception("Max Capacity Currently is $maxCapacity");
        }

        return Transportation::orderBy('capacity')
            ->where('capacity', '>=', $weight)
            ->first();

    }
}
