<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\FeeTypes;
use App\Enums\NotificationTypes;
use App\Enums\OrderStatus;
use App\Models\V1\Order;

final class OrderObserver
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
            OrderStatus::assigned->value => $this->assigned($order),
            OrderStatus::picked->value => $this->picked($order),
            OrderStatus::delivered->value => $this->delivered($order),
            OrderStatus::finished->value => $this->finished($order),
            OrderStatus::rejected->value => $this->rejected($order),
            OrderStatus::canceled->value => $this->canceled($order),
            default => true,
        };
    }

    public function assigned(Order $order)
    {
        // $order->producer->user->notify(
        $order->producer->notify(
            'Order Accepted',
            'Your Order has been accepted',
            ['type' => NotificationTypes::order->value, 'order' => $order->id],
        );
    }

    private function picked(Order $order): void
    {
        // if($order->picked_at !== null){
        //     $order->customer->notify();
        // }
    }

    private function delivered(Order $order): void
    {
        if ($order->delivered_at !== null) {
            $order->codes()->delete();
            $order->producer->user->notify(
                'Order deliverd',
                'Your Order has been delliver',
                ['type' => NotificationTypes::order->value, 'order' => $order->id],
            );
            // $order->customer->notify();
        }
    }

    private function finished(Order $order): void
    {
        $order->carrier->createFee($order, FeeTypes::normal->value);
        $order->carrier->user->notify(
            'Order Finished',
            'Your Order has been finished',
            ['type' => NotificationTypes::order->value, 'order' => $order->id],
        );
    }

    private function canceled(Order $order): void
    {
        $order->codes()->delete();
        // $order->customer->notify();
        if ($order->carrier !== null) {
            // $order->carrier->user->notify(
            $order->carrier->notify(
                'Order canceled',
                'Your Order has been canceled',
                ['type' => NotificationTypes::order->value, 'order' => $order->id],
            );
            $order->producer->createFee($order, FeeTypes::cancel->value);
        }
    }

    private function rejected(Order $order): void
    {
        $order->codes()->delete();
        $order->carrier->createFee($order, FeeTypes::reject->value);
        $order->producer->user->notify(
            'Order rejected',
            'Your Order has been rejected',
            ['type' => NotificationTypes::order->value, 'order' => $order->id],
        );
        // $order->customer->notify();
    }
}
