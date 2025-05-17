<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\V1\Order;
use App\Models\V1\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->producer = User::factory()->create(['role' => 'producer']);
    $this->carrier = User::factory()->create(['role' => 'carrier']);
    $this->producer->badge->update(['is_valid' => 1]);
    $this->carrier->badge->update(['is_valid' => 1]);
    $this->carrierToken = JWTAuth::fromUser($this->carrier);
    $this->producerToken = JWTAuth::fromUser($this->producer);
    $this->url = 'api/v1/order';
    $this->seed();
});

describe('Order Controller', function () {

    it('returns all orders with valid pagination', function () {
        $orders = Order::factory(2)->create();
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->getJson("$this->url/all?page=1&perPage=1");
        expect($res->status())->toBe(200);
        expect($res->json('payload'))
            ->toHaveKeys(['page', 'perPage', 'orders'])
            ->and($res->json('payload.page'))->toBe(1)
            ->and($res->json('payload.perPage'))->toBe(1)
            ->and($res->json('payload.orders'))->toHaveCount(1);
    });

    it('handles missing pagination params gracefully', function () {
        Order::factory(20)->create();
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->getJson("$this->url/all");

        expect($res->status())->toBe(200);
        expect($res->json('payload'))
            ->toHaveKeys(['page', 'perPage', 'orders'])
            ->and($res->json('payload.page'))->toBe(1)
            ->and($res->json('payload.perPage'))->toBe(10)
            ->and($res->json('payload.orders'))->toHaveCount(10);
    });

    it('correctly retrieves an order by ID', function () {
        $order = Order::factory()->create();
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->getJson("$this->url/find?order_id=$order->id");

        expect($res->status())->toBe(200)
            ->and($res->json('payload'))->toHaveKey('order')
            ->and($res->json('payload.order'))->not->toBeNull();
    });

    it('fails when fetching an order without ID', function () {
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->getJson("$this->url/find");
        expect($res->status())->toBe(422);
    });

    it('fails when fetching an order with invalid ID', function () {
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->getJson("$this->url/find?order_id=1000");
        expect($res->status())->toBe(422);
    });

    it('create an order for producer', function () {
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson(
                "$this->url/create",
                [
                    'branch_id' => 1,
                    'customer_id' => 1,
                    'delivery_type' => 'normal',
                    'goods_price' => 100000,
                    'src_long' => 33.524680,
                    'src_lat' => 36.317824,
                    'dest_long' => 33.524680,
                    'dest_lat' => 36.317824,
                    'weight' => 2,
                    'distance' => 690,
                    'cost' => 1709,
                    'customer_name' => 'teffst',
                    'customer_phone' => '0987654321',
                ]
            );
        // dd($res);
        expect($res->status())->toBe(200)
            ->and($res->json('payload'))->toHaveKey('order');
    });

    it('prevent producer to create order with invalid data', function () {
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/create", []);
        expect($res->status())->toBe(422);
    });

    it('prevent carrier to create order', function () {
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/create", []);
        expect($res->status())->toBe(403);
    });

    it('checks the cost of order for producer', function () {
        $check = [
            'branch_id' => 1,
            'delivery_type' => 'normal',
            'weight' => 1500,
            'dest_long' => 31.121212,
            'dest_lat' => 31.212121,
        ];
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/checkCost", $check);
        expect($res->status())->toBe(200)
            ->and($res->json('payload'))->toHaveKey('receipt')
            ->and($res->json('payload.receipt'))->toHaveKeys(['inital', 'final', 'dte']);
    });

    it('fails to checks the cost of order for producer with invalid data', function () {
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/checkCost", []);
        expect($res->status())->toBe(422);
    });

    it('fails to checks the cost of order for carrier', function () {
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/checkCost", []);
        expect($res->status())->toBe(403);
    });

    it('allow carrier to accept order when pending', function () {
        $order = Order::factory()->create([
            'transportation_id' => $this->carrier->badge->transportation_id,
            'carrier_id' => null,
        ]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/accept", ['order_id' => $order->id]);
        expect($res->status())->toBe(200);

        expect($order->fresh()->status)->toBe(OrderStatus::assigned->value);
    });

    it('prevent carrier to accept order when not pending', function () {
        $order = Order::factory()->create(['status' => OrderStatus::picked->value]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/accept", ['order_id' => $order->id]);
        expect($res->status())->toBe(400);
    });

    it('prevent carrier to accept order when order is assigned', function () {
        $order = Order::factory()->create(['carrier_id' => 2]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/accept", ['order_id' => $order->id]);
        expect($res->status())->toBe(400);
    });

    it('prevent carrier to accept order when transportations mismatch', function () {
        $order = Order::factory()->create();
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/accept", ['order_id' => $order->id]);
        expect($res->status())->toBe(400);
    });

    it('fails to accept order for producer', function () {
        $order = Order::factory()->create();
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/accept", ['order_id' => $order->id]);
        expect($res->status())->toBe(403);

        expect($order->fresh()->status)->toBe(OrderStatus::pending->value);
    });

    it('allow producer to cancel pending orders', function () {
        $order = Order::factory()->create([
            'producer_id' => $this->producer->badge->id,
            'carrier_id' => null
        ]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/cancel", ['order_id' => $order->id]);
        expect($res->status())->toBe(200);

        expect($order->fresh()->status)->toBe(OrderStatus::canceled->value);
    });

    it('prevent producer to cancel not pending orders', function () {
        $order = Order::factory()->create([
            'producer_id' => $this->producer->badge->id,
            'status' => OrderStatus::assigned->value,
        ]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/cancel", ['order_id' => $order->id]);
        expect($res->status())->toBe(400);

        expect($order->fresh()->status)->toBe($order->status);
    });

    it('prevent producer to cancel assigned orders', function () {
        $order = Order::factory()->create([
            'producer_id' => $this->producer->badge->id,
            'carrier_id' => 1,
            'status' => OrderStatus::assigned->value,
        ]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/cancel", ['order_id' => $order->id]);
        expect($res->status())->toBe(400);
        expect($order->fresh()->status)->toBe($order->status);
    });

    it('allow carrier to pickup order when assigned', function () {
        $order = Order::factory()->create([
            'carrier_id' => $this->carrier->badge->id,
            'status' => OrderStatus::assigned->value,
        ]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/picked", ['order_id' => $order->id]);
        expect($res->status())->toBe(200);

        expect($order->fresh()->status)->toBe(OrderStatus::picked->value);
    });

    it('prevent carrier to pickup order when status not equal assigned', function () {
        $order = Order::factory()->create([
            'carrier_id' => $this->carrier->badge->id,
            'status' => OrderStatus::pending->value,
        ]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/picked", ['order_id' => $order->id]);
        expect($res->status())->toBe(400);
        expect($order->fresh()->status)->toBe(OrderStatus::pending->value);
    });

    it('prevent carrier to pickup order when not assigned to him', function () {
        $order = Order::factory()->create(['carrier_id' => 15, 'status' => OrderStatus::assigned->value]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/picked", ['order_id' => $order->id]);
        expect($res->status())->toBe(404);

        expect($order->fresh()->status)->toBe(OrderStatus::assigned->value);
    });

    it('prevent producer to pickup order', function () {
        $order = Order::factory()->create([
            'carrier_id' => $this->carrier->badge->id,
            'status' => OrderStatus::assigned->value,
        ]);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->producerToken"])
            ->postJson("$this->url/picked", ['order_id' => $order->id]);
        expect($res->status())->toBe(403);

        expect($order->fresh()->status)->toBe(OrderStatus::assigned->value);
    });

    it('allow carrier to deliver order when picked', function () {
        $order = Order::factory()->create([
            'carrier_id' => $this->carrier->badge->id,
            'status' => OrderStatus::picked->value,
        ]);
        $order->createCode('delivered', 4);
        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/delivered", [
                'order_id' => $order->id,
                'code' => $order->code('delivered')->code,
            ]);
        expect($res->status())->toBe(200);

        expect($order->fresh()->status)->toBe(OrderStatus::delivered->value);
    });

    it('prevent carrier to deliver order when not picked', function () {
        $order = Order::factory()->create([
            'carrier_id' => $this->carrier->badge->id,
            'status' => OrderStatus::assigned->value,
        ]);
        $order->createCode('delivered', 4);

        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/delivered", [
                'order_id' => $order->id,
                'code' => $order->code('delivered')->code,
            ]);
        expect($res->status())->toBe(400);

        expect($order->fresh()->status)->toBe(OrderStatus::assigned->value);
    });

    it('prevent carrier to deliver order with invalid code', function () {
        $order = Order::factory()->create([
            'carrier_id' => $this->carrier->badge->id,
            'status' => OrderStatus::picked->value,
        ]);

        $res = $this->withHeaders(['Authorization' => "Bearer $this->carrierToken"])
            ->postJson("$this->url/delivered", [
                'order_id' => $order->id,
                'code' => 000,
            ]);
        expect($res->status())->toBe(422);

        expect($order->fresh()->status)->toBe(OrderStatus::picked->value);
    });

    // Todo: imporelemt test for finish, force cancel, reject
});
