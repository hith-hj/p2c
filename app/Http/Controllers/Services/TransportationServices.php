<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use App\ExceptionHandler;
use App\Models\V1\Transportation;
use Exception;
use Illuminate\Support\Collection;

class TransportationServices
{
    use ExceptionHandler;

    public function all(): Collection|Exception
    {
        $transportaions = Transportation::all();
        $this->NotFound($transportaions, __('main.transportations'));

        return $transportaions;
    }

    public function find(int $id): Transportation|Exception
    {
        $this->Required($id, __('main.carrier').' ID');
        $transportaion = Transportation::where('id', $id)->first();
        $this->NotFound($transportaion, __('main.transportation'));

        return $transportaion;
    }

    public function getMatchedTransportation(int $weight): Transportation|Exception
    {
        $maxCapacity = Transportation::max('capacity');

        if ($weight > $maxCapacity) {
            throw new Exception("Max Capacity Currently is $maxCapacity");
        }

        return Transportation::orderBy('capacity')
            ->where('capacity', '>=', $weight)
            ->first();

    }
}
