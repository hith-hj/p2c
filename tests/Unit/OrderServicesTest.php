<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Http\Controllers\Services\OrderServices;
use App\Models\V1\Order;
use App\Models\V1\User;
use Illuminate\Pagination\Paginator;

beforeEach(function () {
    $this->orderServices = new OrderServices();
    $this->carrier = User::factory()->create(['role' => 'carrier']);
    $this->producer = User::factory()->create(['role' => 'producer']);
    $carrierOrders = Order::factory(2)->create(['carrier_id' => $this->carrier->id]);
    $producerOrders = Order::factory(2)->create(['producer_id' => $this->producer->id]);
    $this->carrier->badge->orders()->saveMany($carrierOrders);
    $this->producer->badge->orders()->saveMany($producerOrders);
});

describe('Order Services', function () {

    it('retrieves all bending orders for Carrier', function () {
        $result = $this->orderServices->all();
        expect($result)->toBeInstanceOf(Paginator::class)->toHaveCount(4);
    });

    it('fail to retrieves all bending orders for Carrier when no order exists', function () {
        Order::all()->delete();
        $this->orderServices->all();
    })->throws(Exception::class);

    it('retrieves orders for a specific producer', function () {
        $result = $this->orderServices->get($this->producer->badge);
        expect($result)->toBeInstanceOf(Paginator::class);
    });

    it('fail to retrieves orders for specific producer when no order exists', function () {
        Order::all()->delete();
        $this->orderServices->get($this->producer->badge);
    })->throws(Exception::class);

    it('retrieves orders for a specific carrier', function () {
        $result = $this->orderServices->get($this->carrier->badge);
        expect($result)->toBeInstanceOf(Paginator::class);
    });

    it('fail to retrieves orders for specific carrier when no order exists', function () {
        Order::all()->delete();
        $this->orderServices->get($this->carrier->badge);
    })->throws(Exception::class);

    it('retrieves an order by ID', function () {
        $order = Order::factory()->create();

        $result = $this->orderServices->find($order->id);

        expect($result)->toBeInstanceOf(Order::class)->id->toBe($order->id);
    });

    it('fail to retrieves an order by invalid ID', function () {
        $this->orderServices->find(9999);
    })->throws(Exception::class);

    it('calculates the cost of an order', function () {

        $data = $this->orderServices->calcCost(
            $this->producer->badge,
            100,
            1,
            50.0,
            60.0,
            'normal',
        );

        expect($data)->toBeArray()
            ->toHaveKeys(['distance:m', 'weight:kg', 'rounded', 'delivery', 'attrs', 'final']);
    });

    it('creates a new order', function () {
        $data = [
            'branch_id' => 1,
            'weight' => 100,
            'customer_name' => 'John Doe',
            'delivery_type' => 'normal',
            'goods_price' => 1000,
            'dest_long' => 50.0,
            'dest_lat' => 60.0,
            'distance' => 10.0,
            'cost' => 200.0,
        ];
        $order = $this->orderServices->create($this->producer->badge, $data);

        expect($order)->toBeInstanceOf(Order::class);
    });

    it('fail to creates a new order with wrong data', function () {
        $data = [
            'branch_id' => 1,
            'weight' => 100,
            'customer_name' => 'John Doe',
            // 'delivery_type' => 'normal',
            // 'goods_price' => 1000,
            'dest_long' => 50.0,
            'dest_lat' => 60.0,
            'distance' => 10.0,
            'cost' => 200.0,
        ];
        $this->orderServices->create($this->producer->badge, $data);
    })->throws(Exception::class);

    it('accepts an order for a carrier', function () {
        Order::find(1)->update([
            'status' => 0,
            'carrier_id' => null,
            'transportation_id' => $this->carrier->badge->transportation_id,
        ]);
        $result = $this->orderServices->accept($this->carrier->badge, 1);
        expect($result)->toBeInstanceOf(Order::class);
    });

    it('fail to accepts an order for a carrier if order is assigned', function () {
        $this->orderServices->accept($this->carrier->badge, 1);
    })->throws(Exception::class);

    it('fail to accepts an order for a carrier if order status not bending', function () {
        Order::find(1)->update([
            'status' => 1,
            'carrier_id' => null,
        ]);
        $this->orderServices->accept($this->carrier->badge, 1);
    })->throws(Exception::class);

    it('fail to accepts an order for a carrier if transportation mismatch', function () {
        Order::find(1)->update([
            'status' => 1,
            'carrier_id' => null,
        ]);
        $this->orderServices->accept($this->carrier->badge, 1);
    })->throws(Exception::class);

    it('picks an assigned order', function () {
        $order = Order::find(1);
        $order->update(['status' => 1]);
        $result = $this->orderServices->picked($this->carrier->badge, 1);
        expect($result)->toBeInstanceOf(Order::class);
    });

    it('fail to picks order for carrier if not found', function () {
        $this->orderServices->accept($this->carrier->badge, 100);
    })->throws(Exception::class);

    it('fail to pick an order for a carrier if not assigned', function () {
        $order = Order::find(1);
        $order->update(['status' => 0, 'carrier_id' => null]);
        $this->orderServices->accept($this->carrier->badge, 1);
    })->throws(Exception::class);

    it('delivers a picked order', function () {
        $order = Order::find(1);
        $order->update(['status' => OrderStatus::picked->value]);
        $result = $this->orderServices->delivered($this->carrier->badge, 1, $order->code('delivered')->code);

        expect($result)->toBeInstanceOf(Order::class);
    });

    it('fail to delivers a picked order if not found', function () {
        $this->orderServices->delivered($this->carrier->badge, 1000, 0000);
    })->throws(Exception::class);

    it('fail to delivers a picked order if not picked', function () {
        $order = Order::find(1);
        $order->update(['status' => OrderStatus::assigned->value]);
        $this->orderServices->delivered($this->carrier->badge, 1000, 0000);
    })->throws(Exception::class);

    it('fail to delivers a picked order with wrong code', function () {
        $order = Order::find(1);
        $order->update(['status' => OrderStatus::picked->value]);
        $this->orderServices->delivered($this->carrier->badge, 1000, 0000);
    })->throws(Exception::class);
});
