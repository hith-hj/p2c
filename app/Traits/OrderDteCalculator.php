<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\OrderDeliveryTypes;
use Carbon\Carbon;

trait OrderDteCalculator
{
    public function storeDte(): bool
    {
        return $this->update(['dte' => $this->Dte($this)['dte']->toDateTimeString()]);
    }

    /**
     * this trait calculate Order Derlivery Time estimation
     **/
    public function Dte(object|array $order): array
    {
        if ($order === null) {
            $order = $this;
        }
        $order = $this->checkOrderFieldsExists($order, ['created_at', 'delivery_type', 'distance']);
        $delivery_type = $order->delivery_type;
        $distance_In_Km = $order->distance / 1000;
        $dte = $this->getCreatedAt($order);
        $pickupTime = $this->getPickupTimePerKm($delivery_type, $distance_In_Km);
        $deliveryTime = $this->getDeliveryTimePerKm($delivery_type, $distance_In_Km);
        $dte->addHours($pickupTime + $deliveryTime);
        [$dte, $nightAdjusted] = $this->adjustForNight($dte);

        return [
            'dte' => $dte,
            'night_adjusted' => $nightAdjusted,
            'delivery_date' => $dte->format('Y-m-d'),
            'delivery_time' => $dte->format('H:i'),
            'remaining_days' => now()->diff($dte)->d,
            'remaining_hours' => now()->diff($dte)->h,
        ];
    }

    private function checkOrderFieldsExists(object|array $order, $requiredFields = []): object
    {
        if (is_array($order)) {
            $order = (object) $order;
        }
        $missing = [];
        foreach ($requiredFields as $field) {
            if (! isset($order->$field)) {
                $missing[] = $field;
            }
        }
        throw_if(! empty($missing), __('main.Order missing fields: ').implode(', ', $missing));

        return $order;
    }

    private function getCreatedAt(object|array $order): Carbon
    {
        return $order->created_at instanceof Carbon ?
            $order->created_at :
            Carbon::parse($order->created_at);
    }

    private function adjustForNight(Carbon $dte, int $start = 20, int $end = 6): array
    {
        $adjusted = false;
        if ($start > $end) {
            $windowStart = $dte->copy()->setTime($start, 0);
            $windowEnd = $dte->copy()->addDay()->setTime($end, 0);
            if ($dte->gte($windowStart) && $dte->lt($windowEnd)) {
                $dte = $dte->copy()->addDay()->setTime($end, 0);
                $adjusted = true;
            }
        } else {
            $windowStart = $dte->copy()->setTime($start, 0);
            $windowEnd = $dte->copy()->setTime($end, 0);
            if ($dte->between($windowStart, $windowEnd, true)) {
                $dte->setTime($end, 0);
                $adjusted = true;
            }
        }

        return [$dte, $adjusted];
    }

    private function getPickupTimePerKm(string $deliveryType, float $distance): int
    {
        return $this->getDeliveryTimePerKm($deliveryType, $distance);
    }

    private function getDeliveryTimePerKm(string $deliveryType, float $distance = 1): int
    {
        $base = match ($deliveryType) {
            OrderDeliveryTypes::normal->value => config('settings.normal_delivery_time_per_km', 2),
            OrderDeliveryTypes::urgent->value => config('settings.urgent_delivery_time_per_km', 1),
            OrderDeliveryTypes::express->value => config('settings.express_delivery_time_per_km', 0.5),
            default => 3,
        };

        return (int) round($base * $distance);
    }
}
