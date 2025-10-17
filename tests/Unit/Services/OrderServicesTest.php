<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Services\OrderServices;
use App\Models\V1\Order;
use App\Models\V1\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->orderServices = new OrderServices();
    $this->carrier = User::factory()->create(['role' => 'carrier']);
    $this->producer = User::factory()->create(['role' => 'producer']);
    $carrierOrders = Order::factory(2)->create([
        'carrier_id' => $this->carrier->badge->id,
        'producer_id' => $this->producer->badge->id,
    ]);
    $producerOrders = Order::factory(2)->create([
        'carrier_id' => $this->carrier->badge->id,
        'producer_id' => $this->producer->badge->id,
    ]);
    $this->carrier->badge->orders()->saveMany($carrierOrders);
    $this->producer->badge->orders()->saveMany($producerOrders);
    $this->orderData = [
        'branch_id' => 1,
        'weight' => 100,
        'customer_name' => 'John Doe',
        'customer_phone' => '0987654321',
        'delivery_type' => 'normal',
        'goods_price' => 1000,
        'dest_long' => 50.0,
        'dest_lat' => 60.0,
        'distance' => 10.0,
        'cost' => 200.0,
    ];
});

describe('Order Services', function () {

    it('retrieves all orders with bending status', function () {
        $res = $this->orderServices->all();
        expect($res)->toBeInstanceOf(Collection::class)->toHaveCount(4);
    });

    it('fail to retrieves all bending orders for Carrier when no order exists', function () {
        Order::truncate();
        $this->orderServices->all();
    })->throws(NotFoundHttpException::class);

    it('retrieves orders for a specific producer', function () {
        $res = $this->orderServices->get($this->producer->badge);
        expect($res)->toBeInstanceOf(Collection::class)->toHaveCount(4);
    });

    it('fail to retrieves orders for specific producer when no order exists', function () {
        Order::truncate();
        $this->orderServices->get($this->producer->badge);
    })->throws(NotFoundHttpException::class);

    it('fail to retrieves orders for specific producer with invalid badge', function () {
        Order::truncate();
        $this->orderServices->get((object) []);
    })->throws(Exception::class);

    it('fail to retrieves orders for null badge', function () {
        Order::truncate();
        $this->orderServices->get();
    })->throws(TypeError::class);

    it('retrieves orders for a specific carrier', function () {
        $res = $this->orderServices->get($this->carrier->badge);
        expect($res)->toBeInstanceOf(Collection::class)->toHaveCount(4);
    });

    it('fail to retrieves orders for specific carrier when no order exists', function () {
        Order::truncate();
        $this->orderServices->get($this->carrier->badge);
    })->throws(NotFoundHttpException::class);

    it('retrieves an order by ID', function () {
        $order = Order::factory()->create();
        $res = $this->orderServices->find($order->id);
        expect($res)->toBeInstanceOf(Order::class)->id->toBe($order->id);
    });

    it('fail to retrieves an order by invalid ID', function () {
        $this->orderServices->find(9999);
    })->throws(NotFoundHttpException::class);

    it('fail to retrieves an order by invalid ID type', function () {
        $this->orderServices->find('9999');
    })->throws(TypeError::class);

    it('fail to retrieves an order by Null ID', function () {
        $this->orderServices->find();
    })->throws(TypeError::class);

    it('calculates the cost of an order', function () {
        $data = $this->orderServices->calcCost(
            $this->producer->badge,
            [
                'weight' => 100,
                'branch_id' => 1,
                'dest_long' => 31.121212,
                'dest_lat' => 31.212121,
                'delivery_type' => 'normal',
            ]
        );
        expect($data)->toBeArray();
    });

    it('fails to calculates cost of an order if producer missing', function () {
        $this->orderServices->calcCost(null, []);
    })->throws(TypeError::class);

    it('fails to calculates cost of an order if any arguments missing', function () {
        $this->orderServices->calcCost($this->producer->badge, []);
    })->throws(Exception::class);

    it('fails to calculates cost of an order if Producer badge is invalid ', function () {
        $this->orderServices->calcCost((object) [], []);
    })->throws(TypeError::class);

    it('fails to calculates cost of an order if distance is out of range', function () {
        $this->orderServices->calcCost(
            $this->producer->badge,
            [
                'weight' => 100,
                'branch_id' => 1,
                'dest_long' => 50.40000,
                'dest_lat' => 40.80000,
                'delivery_type' => 'normal',
            ]
        );
    })->throws(exception::class);

    it('creates a new order', function () {
        $order = $this->orderServices->create($this->producer->badge, $this->orderData);
        expect($order)->toBeInstanceOf(Order::class);
    });

    it('check codes existance for newly created order', function () {
        $order = $this->orderServices->create($this->producer->badge, $this->orderData);
        expect($order)->toBeInstanceOf(Order::class);
        expect($order->codes)->toBeInstanceOf(Collection::class);
        expect($order->fresh()->codes)->toHaveCount(2);
    });

    it('check dte existance for newly created order  ', function () {
        $order = $this->orderServices->create($this->producer->badge, $this->orderData);
        expect($order)->toBeInstanceOf(Order::class)->not->toBeNull();
        expect($order->dte)->not->toBeNull();
    });

    it('check customer existance for newly created order', function () {
        $order = $this->orderServices->create($this->producer->badge, $this->orderData);
        expect($order)->toBeInstanceOf(Order::class)->not->toBeNull();
        expect($order->fresh()->customer)->not->toBeNull();
    });

    it('fail to creates a new order with wrong data', function () {
        $this->orderServices->create($this->producer->badge, []);
    })->throws(Exception::class);

    it('fail to creates a new order with invalid Producer badge', function () {
        $this->orderServices->create((object) [], []);
    })->throws(TypeError::class);

    it('accepts an order for a carrier', function () {
        $order = Order::find(1);
        $order->update([
            'transportation_id' => $this->carrier->badge->transportation_id,
            'status' => 0,
            'carrier_id' => null,
        ]);
        $res = $this->orderServices->accept($this->carrier->badge, $order->id);
        expect($res)->toBeInstanceOf(Order::class)->status->toBe(1);
        expect($order->fresh()->carrier_id)->toBe($this->carrier->badge->id);
        expect($order->fresh()->status)->toBe(1);
    });

    it('modifiy dte field for order when order is accepted', function () {
        $order = Order::find(1);
        $order->update([
            'status' => 0,
            'carrier_id' => null,
            'transportation_id' => $this->carrier->badge->transportation_id,
        ]);
        sleep(1);
        $res = $this->orderServices->accept($this->carrier->badge, $order->id);
        expect($res)->toBeInstanceOf(Order::class);
        expect($order->fresh()->dte)->not->toBeNull();
        expect($order->fresh()->dte)->not->toEqual($order->dte);
    });

    it('fail to accepts an order for a carrier if order is assigned', function () {
        $this->orderServices->accept($this->carrier->badge, 1);
    })->throws(Exception::class);

    it('fail to accepts an order for a carrier if badge is null', function () {
        $this->orderServices->accept((object) [], 1);
    })->throws(TypeError::class);

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
        $res = $this->orderServices->picked($this->carrier->badge, 1);
        expect($res)->toBeInstanceOf(Order::class);
        expect($order->fresh()->status)->toBe(2);
    });

    it('fail to picks order for carrier if not found', function () {
        $this->orderServices->accept($this->carrier->badge, 100);
    })->throws(Exception::class);

    it('fail to picks order for carrier with invalid badge', function () {
        $this->orderServices->accept((object) [], 100);
    })->throws(TypeError::class);

    it('fail to pick an order for a carrier if not assigned', function () {
        $order = Order::find(1);
        $order->update(['status' => 0, 'carrier_id' => null]);
        $this->orderServices->accept($this->carrier->badge, 1);
    })->throws(Exception::class);

    it('delivers a picked order', function () {
        $order = Order::find(1);
        $order->update(['status' => OrderStatus::picked->value]);
        $res = $this->orderServices->delivered(
            $this->carrier->badge,
            $order->id,
            $order->code('delivered')->code
        );
        expect($res)->toBeInstanceOf(Order::class);
        expect($order->fresh()->status)->toBe(3);
    });

    it('fail to delivers a picked order if not found', function () {
        $this->orderServices->delivered($this->carrier->badge, 1000, 0000);
    })->throws(Exception::class);

    it('fail to delivers a picked order with invalid badge', function () {
        $this->orderServices->delivered((object) [], 1000, 0000);
    })->throws(TypeError::class);

    it('fail to delivers a picked order if not picked', function () {
        $order = Order::find(1);
        $order->update(['status' => OrderStatus::assigned->value]);
        $this->orderServices->delivered($this->carrier->badge, 1, 0000);
    })->throws(Exception::class);

    it('fail to delivers a picked order with wrong code', function () {
        $order = Order::find(1);
        $order->update(['status' => OrderStatus::picked->value]);
        $this->orderServices->delivered($this->carrier->badge, 1000, 0000);
    })->throws(Exception::class);
});
