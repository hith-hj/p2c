<?php

declare(strict_types=1);

namespace App;

use App\Models\V1\Attr;
use App\Models\V1\Item;

trait OrderServices
{
    private function calcDistance(array $src, array $dest): int
    {
        $rad = M_PI / 180;

        return (int) round(
            acos(
                sin($src['lat'] * $rad) * sin($dest['lat'] * $rad) +
                cos($src['lat'] * $rad) * cos($dest['lat'] * $rad) *
                cos($src['long'] * $rad - $dest['long'] * $rad)
            ) * 6371, 2); // km
    }

    private function initalCost($trans, int $weight, int $distance): int
    {
        return (int) round(
                $trans->inital_cost +
                $weight * $trans->cost_per_kg +
                $distance * $trans->cost_per_km
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

    private function AttrsCost(array $attrs, string $calcType = 'totla') : int|array
    {
        if ($attrs === []) {
            return 0;
        }

        $query = Attr::whereIn('id', $attrs);

        return match ($calcType) {
            default => $query->sum('extra_cost_percent'),
            'total' => $query->sum('extra_cost_percent'),
            'byone' => $query->pluck('extra_cost_percent')->toArray(),
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

        return 0;
    }

    private function addPercent(int $number, int $percent): int
    {
        return (int) round($number * (1 + $percent / 100));
    }
}
