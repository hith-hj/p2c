<?php

declare(strict_types=1);

use App\Models\V1\Branch;
use App\Models\V1\Fee;
use App\Models\V1\Order;
use App\Models\V1\Producer;
use App\Models\V1\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'producer']);
    $this->user->badge->update(['is_valid' => 1]);
    $token = JWTAuth::fromUser($this->user);
    $this->withHeaders(['Authorization' => "Bearer $token"]);
    $this->url = 'api/v1/producer';
    $this->seed();
    $this->createOrderData = [
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
    ];
    $this->postOrderData = array_merge($this->createOrderData, [
        'customer_name' => 'test',
        'customer_phone' => '0987654321',
    ]);
});
function createOrder($holder, $baseAttrs = [], $extraAttrs = [], $fieldsToUpdate = [])
{
    $baseAttrs = array_merge($baseAttrs, $extraAttrs);
    $order = $holder->orders()->create($baseAttrs);
    $order->update($fieldsToUpdate);

    return $order;
}
describe('Producer Controller', function () {
    it('retrieves all producers', function () {
        User::factory()->count(3)->create(['role' => 'producer']);

        $res = $this->getJson("$this->url/all");

        expect($res->status())->toBe(200);
        expect($res->json('payload.producers'))->toBeArray()->not->toBeEmpty();
    });

    it('retrieves authenticated user producer', function () {
        $res = $this->getJson($this->url);
        expect($res->status())->toBe(200);
        expect($res->json('payload.producer'))->toBeArray()->not->toBeEmpty();
    });

    it('fails to retrieves authenticated user producer if not exists', function () {
        $this->user->badge->delete();
        $res = $this->getJson($this->url);
        expect($res->status())->toBe(404);
    });

    it('finds a specific producer', function () {
        $user = User::factory()->create(['role' => 'producer']);
        expect($user->badge)->not->toBeNull();
        $id = $user->badge->id;
        $res = $this->getJson("$this->url/find?producer_id=$id");
        expect($res->status())->toBe(200);
        expect($res->json('payload.producer'))->toBeArray()->not->toBeEmpty();
    });

    it('fails to find a producer with an invalid ID', function () {
        $res = $this->getJson("$this->url/find?producer_id=999");

        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->toBeArray()->not->toBeEmpty();
    });

    it('creates a new producer', function () {
        $user = User::create([
            'email' => 'test@test.com',
            'phone' => '0911112222',
            'password' => 'password',
            'role' => 'producer',
            'firebase_token' => 'some-token-here',
            'verified_at' => now(),
        ]);
        $token = JWTAuth::fromUser($user);
        $res = $this->actingAs($user)->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson("$this->url/create", [
            'brand' => 'Test Brand',
            'cords' => ['long' => 10.5, 'lat' => 20.3],
        ]);
        expect($res->status())->toBe(200);
        expect($res->json('payload.producer.brand'))->toBe('Test Brand');
    });

    it('prevent user to create a new producer if producer exists', function () {
        $res = $this->postJson("$this->url/create", [
            'brand' => 'Test Brand',
            'cords' => ['long' => 10.5, 'lat' => 20.3],
        ]);
        expect($res->status())->toBe(400);
        expect($res->json('success'))->toBe(false);
        expect($res->json('message'))->not->toBeNull();
    });

    it('updates an existing producer', function () {
        $res = $this->patchJson("$this->url/update", ['brand' => 'New Brand']);
        expect($res->status())->toBe(200);
        expect($this->user->badge->fresh()->brand)->toBe('New Brand');
    });

    it('fails to update a producer with invalid badge', function () {
        $this->user->badge->delete();
        $res = $this->patchJson("$this->url/update", ['brand' => 'New Brand']);
        expect($res->status())->toBe(400);
    });

    it('deletes a producer', function () {
        $res = $this->deleteJson("$this->url/delete");
        expect($res->status())->toBe(200);
        expect(Producer::find($this->user->id))->toBeNull();
    });

    it('fails to deletes a producer with invalid badge', function () {
        $this->user->badge->delete();
        $res = $this->deleteJson("$this->url/delete");
        expect($res->status())->toBe(400);
    });

    it('can create new branch for borducer', function () {
        $data = [
            'name' => 'brach1',
            'phone' => '0912312312',
            'cords' => ['lat' => '11.11', 'long' => '12.12'],
        ];
        $res = $this->postJson('/api/v1/branch/create', $data);
        expect($res->status())->toBe(200);
        expect($res->json('payload.branch'))->not->toBeNull();
    });

    it('allow producer to update branch', function () {
        $data = [
            'name' => 'branch',
            'phone' => '0934555312',
        ];
        $branch = $this->user->badge->branches()->create($data);
        $data2 = [
            'name' => 'branchre',
            'phone' => '0934666312',
        ];
        $res = $this->patchJson("/api/v1/branch/update?branch_id=$branch->id", $data2);
        expect($res->status())->toBe(200);
        expect($res->json('message'))->toBe(__('main.updated'));
    });

    it('prevent producer from set branch as default if not owner', function () {
        $branch = Branch::factory()->create(['producer_id' => 20, 'is_default' => false]);
        $res = $this->postJson("/api/v1/branch/setDefault?branch_id=$branch->id");

        expect($branch->fresh()->is_default)->toBe(0);
        expect($res->status())->toBe(403);
        expect($res->json('message'))->toBe(__('main.unauthorized'));
    });

    it('allow producer to delete branch if not default', function () {
        $branch = $this->user->badge->branches()->where('is_default', false)->first();
        expect($branch)->not->toBeNull();
        expect($branch->is_default)->toBe(0);
        $res = $this->deleteJson("/api/v1/branch/delete?branch_id=$branch->id");

        expect($branch->fresh())->toBeNull();
        expect($res->status())->toBe(200);
        expect($res->json('message'))->toBe(__('main.deleted'));
    });

    it('prevent producer from delete branch if not his', function () {
        $branch = Branch::factory()->create(['producer_id' => 20]);
        expect($branch)->not->toBeNull();
        $res = $this->deleteJson("/api/v1/branch/delete?branch_id=$branch->id");

        expect($res->status())->toBe(403);
        expect($res->json('message'))->toBe(__('main.unauthorized'));
    });

    it('prevent producer from delete branch if default', function () {
        $branch = Branch::factory()->create(['producer_id' => $this->user->badge->id, 'is_default' => true]);
        expect($branch)->not->toBeNull();
        $res = $this->deleteJson("/api/v1/branch/delete?branch_id=$branch->id");

        expect($res->status())->toBe(400);
        expect($res->json('success'))->toBeFalse();
    });

    it('allow producer to create order', function () {
        $res = $this->postJson('/api/v1/order/create', $this->postOrderData);
        expect($res->status())->toBe(200);
        expect($res->json('payload.order'))->not->toBeNull();
        expect($res->json('payload.order.id'))->toBe(1);
    });

    it('prevent invalid producer from create new order', function () {
        $this->user->badge->update(['is_valid' => false]);
        $res = $this->postJson('/api/v1/order/create', $this->createOrderData);
        expect($res->status())->toBe(403);
    });

    it('prevent producer from create new order with invalid badge', function () {
        $this->user->badge->delete();
        $res = $this->postJson('/api/v1/order/create', $this->createOrderData);
        expect($res->status())->toBe(403);
    });

    it('check if codes is created when new order is created', function () {
        $res = $this->postJson('/api/v1/order/create', $this->postOrderData);
        $order = Order::where('id', $res->json('payload.order.id'))->first();
        expect($order->codes)->not()->toBeNull();
        expect($order->codes->toArray())->toBeArray();
        expect($res->status())->toBe(200);
    });

    it('prevent producer to create order with bad info', function () {
        $res = $this->postJson('/api/v1/order/create', []);
        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->not->toBeNull();
    });

    it('allow producer to cancel order when bending', function () {
        $order = createOrder($this->user->badge, $this->createOrderData, ['status' => 0]);
        $res = $this->postJson("/api/v1/order/cancel?order_id=$order->id");
        expect($res->status())->toBe(200);
    });

    it('check if when order is canceled his codes are deleted', function () {
        $order = createOrder($this->user->badge, $this->createOrderData);
        $res = $this->postJson("/api/v1/order/cancel?order_id=$order->id");
        expect($order->codes()->count())->toBe(0);
        expect($res->status())->toBe(200);
    });

    it('prevent producer to cancel order when not bending', function () {
        $order = createOrder($this->user->badge, $this->createOrderData, ['status' => 1]);
        $res = $this->postJson("/api/v1/order/cancel?order_id=$order->id");
        expect($res->status())->toBe(400);
        expect($order->status)->toBe(1);
    });

    it('allow producer to force cancel order when assigned', function () {
        $order = createOrder($this->user->badge, $this->createOrderData, ['status' => 1]);
        $res = $this->postJson("/api/v1/order/forceCancel?order_id=$order->id");
        expect($res->status())->toBe(200);
    });

    it('checks if fee is stored for producer when force cancel order ', function () {
        $order = createOrder($this->user->badge, $this->createOrderData, ['status' => 1, 'carrier_id' => 22]);
        $res = $this->postJson("/api/v1/order/forceCancel?order_id=$order->id");
        expect($res->status())->toBe(200);
        $fee = Fee::where([['subject_id', $order->id], ['subject_type', get_class($order)]])->first();
        expect($fee)->not->toBeNull();
    });

    it('prevent producer from force cancel order when not assigned', function () {
        $order = createOrder($this->user->badge, $this->createOrderData);
        $res = $this->postJson("/api/v1/order/forceCancel?order_id=$order->id");
        expect($res->status())->toBe(400);
    });

    it('allow producer to finish order when delivered', function () {
        $order = createOrder($this->user->badge, $this->createOrderData, ['status' => 3], ['carrier_id' => 1]);
        $res = $this->postJson("/api/v1/order/finish?order_id=$order->id");
        expect($res->status())->toBe(200);
        expect($order->fresh()->status)->toBe(4);
    });

    it('check when order is finished his codes are deleted', function () {
        $order = createOrder($this->user->badge, $this->createOrderData, ['status' => 3], ['carrier_id' => 1]);
        $res = $this->postJson("/api/v1/order/finish?order_id=$order->id");
        expect($res->status())->toBe(200);
        expect($order->fresh()->status)->not->toBe(3);
    });

    it('prevent producer to finish order when not delivered', function () {
        $order = createOrder($this->user->badge, $this->createOrderData);
        $res = $this->postJson("/api/v1/order/finish?order_id=$order->id");
        expect($res->status())->toBe(400);
        expect($order->fresh()->status)->not->toBe(3);
    });

    it('checks if fee stored when order is finished', function () {
        $order = createOrder($this->user->badge, $this->createOrderData, ['status' => 3], ['carrier_id' => 1]);
        $res = $this->postJson("/api/v1/order/finish?order_id=$order->id");
        expect($res->status())->toBe(200);
        expect($order->fresh()->status)->toBe(4);
        $fee = Fee::where([['subject_id', $order->id], ['subject_type', get_class($order)]])->first();
        expect($fee)->not->toBeNull();
        expect($fee->subject_type)->toBe(get_class($order));
        expect($fee->subject_id)->toBe($order->id);
    });
});
