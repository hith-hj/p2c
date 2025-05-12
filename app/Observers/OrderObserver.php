<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\FeeTypes;
use App\Enums\OrderStatus;
use App\Models\V1\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->createCode('pickup', 4);
        $order->createCode('delivered', 4);
        $order->storeDte();
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        match ($order->status) {
            OrderStatus::picked->value => $this->picked($order),
            OrderStatus::delivered->value => $this->delivered($order),
            OrderStatus::finished->value => $this->finished($order),
            OrderStatus::rejected->value => $this->rejected($order),
            OrderStatus::canceld->value => $this->canceld($order),
            default => true,
        };
    }

    private function picked(Order $order): void
    {
        // if($order->picked_at !== null){
        //     $order->customer->notifiPhone();
        // }
    }

    private function delivered(Order $order): void
    {
        if ($order->delivered_at !== null) {
            $order->codes()->delete();
            // $order->customer->notifiPhone();
        }
    }

    private function finished(Order $order): void
    {
        $order->createFee($order->carrier, FeeTypes::normal->value);
    }

    private function canceld(Order $order): void
    {
        $order->codes()->delete();
        if ($order->carrier_id !== null) {
            $order->createFee($order->producer, FeeTypes::cancel->value);
            // $order->customer->notifiPhone();
        }
    }

    private function rejected(Order $order): void
    {
        $order->codes()->delete();
        $order->createFee($order->carrier, FeeTypes::reject->value);
        // $order->customer->notifiPhone();
    }
}
