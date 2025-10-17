<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\TransportationServices;
use App\Models\V1\Attr;
use App\Models\V1\Branch;
use App\Models\V1\Item;

trait OrderCostCalculator
{
    private function getCost(
        Branch $branch,
        array $data
    ): array {
        $distance = $this->calcDistance(
            src: ['lat' => $branch->location->lat, 'long' => $branch->location->long],
            dest: ['lat' => $data['dest_lat'], 'long' => $data['dest_long']]
        );
        $distanceInMeter = $distance * 1000;
        $this->checkIfValidDistance($distanceInMeter);
        $transportation = $this->getTransportation($data['weight']);
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

    private function checkIfValidDistance(int $distanceInMeter): void
    {
        $minRange = config('settings.min_order_distance.value', 300);
        $maxRange = config('settings.max_order_distance.value', 50000);
        throw_if(
            $distanceInMeter < $minRange || $distanceInMeter > $maxRange,
            __("main.Distance should be between $minRange and $maxRange meter, your is : $distanceInMeter")
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

    private function AttrsCost(array $data): int|array
    {
        $calcType = config('settings.order_attrs_calculation_type.value', 'totla');
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

    private function ItemsCost(array $items): int|array
    {
        $calcType = config('settings.order_items_calculation_type.value', 'totla');
        if ($items === []) {
            return 0;
        }

        $query = Item::whereIn('id', $items);

        return match ($calcType) {
            'byone' => $query->pluck('extra_cost_percent')->toArray(),
            'total' => $query->sum('extra_cost_percent'),
            default => $query->sum('extra_cost_percent'),
        };
    }

    private function deliveryTypeCost(string $delivery_type): int
    {
        if ($delivery_type === 'urgent') {
            return config('settings.urgent_order_cost.value', 20);
        }
        if ($delivery_type === 'express') {
            return config('settings.express_order_cost.value', 35);
        }

        return 0;
    }

    private function addPercent(int $number, int $percent): int
    {
        return (int) round($number * (1 + $percent / 100));
    }
}
