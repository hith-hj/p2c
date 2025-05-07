<?php

declare(strict_types=1);

namespace App\Traits;

use App\Http\Services\TransportationServices;
use App\Models\V1\Attr;
use App\Models\V1\Branch;
use App\Models\V1\Item;

trait OrderCostCalculator
{
    private function getCost(
        Branch $branch,
        array $data
    ): array {
        $transportation = $this->getTransportation($data['weight']);
        $distance = $this->calcDistance(
            src: ['lat' => $branch->location->lat, 'long' => $branch->location->long],
            dest: ['lat' => $data['dest_lat'], 'long' => $data['dest_long']]
        );
        $distanceInMeter = $distance * 1000;
        $this->checkIfValidDistance($distanceInMeter);
        $inital = $this->initalCost($transportation, $data['weight'], $distance);
        $delivery = $this->deliveryTypeCost($data['delivery_type']);
        $attrs = $this->AttrsCost($data);
        $final = $this->finalCost($inital, $attrs, $delivery);

        return [
            'distance:m' => $distanceInMeter,
            'inital' => $inital,
            'delivery' => $delivery,
            'attrs' => $attrs,
            'final' => $final,
        ];
    }

    private function getTransportation(int $weight)
    {
        return (new TransportationServices())->getMatchedTransportation($weight);
    }

    private function calcDistance(array $src, array $dest): int
    {
        $rad = M_PI / 180;

        return (int) round(
            acos(
                sin($src['lat'] * $rad) * sin($dest['lat'] * $rad) +
                    cos($src['lat'] * $rad) * cos($dest['lat'] * $rad) *
                    cos($src['long'] * $rad - $dest['long'] * $rad)
            ) * 6371,
            2
        ); // km
    }

    private function checkIfValidDistance(int $distanceInMeter, int $minRange = 300, int $maxRange = 50000): void
    {
        throw_if(
            $distanceInMeter < $minRange || $distanceInMeter > $maxRange,
            'Exception',
            __("main.Distance should be between 200 and 50000 meter, your is : $distanceInMeter")
        );
    }

    private function initalCost(object $transportation, int $weight, int $distance): int
    {
        return (int) round(
            $transportation->inital_cost +
                $weight * $transportation->cost_per_kg +
                $distance * $transportation->cost_per_km
        );
    }

    private function finalCost(int $cost, array|int $attrs, int $delivery): int
    {
        $final = $cost;
        if (is_array($attrs)) {
            foreach ($attrs as $attr) {
                $final = $this->addPercent($final, $attr);
            }
        } else {
            $final = $this->addPercent($final, $attrs);
        }

        $final = $this->addPercent($final, $delivery);

        return (int) floor($final);
    }

    private function AttrsCost(array $data, string $calcType = 'totla'): int|array
    {
        if (! isset($data['attrs']) || $data['attrs'] === []) {
            return 0;
        }

        $query = Attr::whereIn('id', $data['attrs']);

        return match ($calcType) {
            'byone' => $query->pluck('extra_cost_percent')->toArray(),
            'total' => $query->sum('extra_cost_percent'),
            default => $query->sum('extra_cost_percent'),
        };
    }

    private function ItemsCost(array $items, string $calcType = 'totla'): int|array
    {
        if ($items === []) {
            return 0;
        }

        $query = Item::whereIn('id', $items);

        return match ($calcType) {
            default => $query->sum('extra_cost_percent'),
            'total' => $query->sum('extra_cost_percent'),
            'byone' => $query->pluck('extra_cost_percent')->toArray(),
        };
    }

    private function deliveryTypeCost(string $delivery_type, int $percent = 20): int
    {
        if ($delivery_type === 'urgent') {
            return $percent;
        }
        if ($delivery_type === 'express') {
            return $percent * 15 / 10;
        }

        return 0;
    }

    private function addPercent(int $number, int $percent): int
    {
        return (int) round($number * (1 + $percent / 100));
    }
}
