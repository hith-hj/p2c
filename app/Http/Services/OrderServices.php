<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Enums\OrderDeliveryTypes;
use App\Enums\OrderStatus;
use App\ExceptionHandler;
use App\Models\V1\Branch;
use App\Models\V1\Carrier;
use App\Models\V1\Order;
use App\Models\V1\Producer;
use App\OrderCostCalculator;
use App\OrderDteCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OrderServices
{
    use ExceptionHandler;
    use OrderCostCalculator;
    use OrderDteCalculator;

    public function all(
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = []
    ): Collection {
        $orders = Order::query()
            ->with(['attrs', 'items', 'producer', 'carrier', 'transportation', 'branch', 'customer'])
            ->where('status', OrderStatus::pending->value);
        $this->applyFilters($orders, $filters, ['delivery_type' => OrderDeliveryTypes::values()]);
        $this->applyOrderBy($orders, $orderBy, ['cost', 'distance', 'weight']);
        $orders->paginate(perPage: $perPage, page: $page);
        $orders = $orders->get();
        $this->Truthy($orders->isEmpty(), 'orders');

        return $orders;
    }

    public function get(
        object $badge,
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = []
    ): Collection {
        $orders = $badge->orders();
        $orders->with(['attrs', 'items', 'producer', 'carrier', 'transportation', 'branch', 'customer']);
        $this->applyFilters($orders, $filters, ['status' => OrderStatus::values()]);
        $this->applyOrderBy($orders, $orderBy, ['cost', 'distance', 'weight']);
        $orders->paginate(perPage: $perPage, page: $page);
        $orders = $orders->get();
        $this->Truthy($orders->isEmpty(), 'orders');

        return $orders;
    }

    public function find(int $id): Order
    {
        $order = Order::find($id);
        $this->Truthy($order === null, 'order');

        return $order;
    }

    public function calcCost(Producer $producer, array $data): array
    {
        $data = $this->checkAndCastData($data, [
            'weight' => 'int',
            'branch_id' => 'int',
            'dest_long' => 'float',
            'dest_lat' => 'float',
            'delivery_type' => 'string',
        ]);
        $branch = (new BranchServices())->find($data['branch_id']);
        $this->chackIfValidBranchWithLocation($branch, $producer);
        $cost = $this->getCost($branch, $data);
        // todo : use transportation type to imporve dte
        $dte = $this->Dte([
            'created_at' => now(),
            'delivery_type' => $data['delivery_type'],
            'distance' => $cost['distance:m'],
        ]);

        return [
            'distance:m' => $cost['distance:m'],
            'inital' => $cost['inital'],
            'delivery' => $cost['delivery'],
            'attrs' => $cost['attrs'],
            'final' => $cost['final'],
            'dte' => $dte,
        ];
    }

    public function create(Producer $producer, array $data): Order
    {
        $data = $this->checkAndCastData($data, [
            'customer_phone' => 'string',
            'customer_name' => 'string',
            'delivery_type' => 'string',
            'dest_long' => 'float',
            'dest_lat' => 'float',
            'goods_price' => 'int',
            'branch_id' => 'int',
            'distance' => 'int',
            'weight' => 'int',
            'cost' => 'int',
        ]);
        $branch = $producer->branches()->find($data['branch_id']);
        $this->chackIfValidBranchWithLocation($branch, $producer);
        $transportation = $this->getTransportation($data['weight']);
        $order = $producer->orders()->create([
            'branch_id' => $branch->id,
            'src_long' => $branch->location->long,
            'src_lat' => $branch->location->lat,
            'transportation_id' => $transportation->id,
            'delivery_type' => $data['delivery_type'],
            'goods_price' => $data['goods_price'],
            'dest_long' => round($data['dest_long'], 8),
            'dest_lat' => round($data['dest_lat'], 8),
            'distance' => $data['distance'],
            'weight' => $data['weight'],
            'cost' => $data['cost'],
            'note' => $data['note'] ?? null,
        ]);
        $this->attachRelations($order, $data);
        $order->storeDte();
        return $order;
    }

    public function accept(Carrier $carrier, int $order_id): Order
    {
        $order = $this->find($order_id);
        $this->Truthy($order === null, 'Not found');
        $this->Truthy($order->carrier_id !== null, 'order is assigned');
        $this->Truthy($order->status !== OrderStatus::pending->value, 'invalid order status');
        $this->Truthy($order->transportation_id !== $carrier->transportation_id, 'transportations is not match');
        $order->carrier()->associate($carrier);
        $dte = $this->Dte([
            'created_at' => now(),
            'delivery_type' => $order->delivery_type,
            'distance' => $order->distance,
        ]);
        $order->update([
            'status' => OrderStatus::assigned->value,
            'dte' => $dte['dte']->toDateTimeString()
        ]);

        return $order;
    }

    public function picked(Carrier $carrier, int $order_id): Order
    {
        $order = $carrier->orders()->find($order_id);
        $this->Truthy($order === null, 'Not found');
        $this->Truthy($order->status !== OrderStatus::assigned->value, 'invalid order status');
        $order->update([
            'status' => OrderStatus::picked->value,
            'picked_at' => now()->toDateTimeString(),
        ]);

        return $order;
    }

    public function delivered(Carrier $carrier, int $order_id, int $code): Order
    {
        $order = $carrier->orders()->find($order_id);
        $this->Truthy($order === null, 'Not found');
        $this->Truthy($order->status !== OrderStatus::picked->value, 'invalid order status');
        $this->Truthy($order->code('delivered')->code !== $code, 'invalid code');
        $order->update([
            'status' => OrderStatus::delivered->value,
            'delivered_at' => now()->toDateTimeString(),
        ]);

        return $order;
    }

    public function finish(Producer $producer, int $order_id): Order
    {
        $order = $producer->orders()->find($order_id);
        $this->Truthy($order === null, 'Not found');
        $this->Truthy($order->status !== OrderStatus::delivered->value, 'invalid order status');
        $order->update(['status' => OrderStatus::finished->value]);
        $order->codes()->delete();
        $order->createFee($order->carrier);

        return $order;
    }

    public function cancel(Producer $producer, int $order_id): Order
    {
        $order = $producer->orders()->find($order_id);
        $this->Truthy($order === null, 'Not found');
        $this->Truthy($order->carrier_id !== null, 'order is assigned');
        $this->Truthy($order->status !== OrderStatus::pending->value, 'invalid order status');
        $order->update(['status' => OrderStatus::canceld->value]);
        $order->codes()->delete();

        return $order;
    }

    public function forceCancel(Producer $producer, int $order_id): Order
    {
        $order = $producer->orders()->find($order_id);
        $this->Truthy($order === null, 'Not found');
        $this->Truthy($order->status !== OrderStatus::assigned->value, 'invalid order status');
        $order->update(['status' => OrderStatus::canceld->value]);
        $order->codes()->delete();
        $order->createFee($producer);

        return $order;
    }

    public function reject(Carrier $carrier, int $order_id): Order
    {
        $order = $carrier->orders()->find($order_id);
        $this->Truthy($order === null, 'Not found');
        $this->Truthy($order->status !== OrderStatus::assigned->value, 'invalid order status');
        $order->update(['status' => OrderStatus::rejected->value]);
        $order->codes()->delete();
        $order->createFee($carrier);

        return $order;
    }

    private function applyFilters(object $query, array $filters, array $allowedFilters = []): object
    {
        return $query->when(! empty($filters), function (Builder $filter) use ($filters, $allowedFilters) {
            foreach ($filters as $key => $value) {
                if (! in_array($key, array_keys($allowedFilters))) {
                    return;
                }
                if (is_numeric($value)) {
                    $value = (int) $value;
                }
                if (in_array($value, $allowedFilters[$key])) {
                    $filter->where($key, $value);
                }
            }
        });
    }

    private function applyOrderBy(object $query, array $orderBy, array $allowedOrderBy = []): object
    {
        return $query->when(
            ! empty($orderBy) && count($orderBy) === 1,
            function (Builder $query) use ($orderBy, $allowedOrderBy) {
                $key = array_key_first($orderBy);
                $value = array_pop($orderBy);
                if (in_array($key, $allowedOrderBy) && in_array($value, ['asc', 'desc'])) {
                    $query->orderBy($key, $value);
                }
            }
        );
    }

    private function checkAndCastData(array $data, $requiredFields = []): array
    {
        $this->Truthy(empty($data), 'data is empty');
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = array_diff(array_keys($requiredFields), array_keys($data));
        $this->Falsy(empty($missing), 'fields missing: ' . implode(', ', $missing));
        foreach ($requiredFields as $key => $value) {
            settype($data[$key], $value);
        }

        return $data;
    }

    private function attachRelations(Order $order, array $data): Order
    {
        if (isset($data['attrs']) && ! empty($data['attrs'])) {
            $order->attrs()->attach($data['attrs']);
        }
        if (isset($data['items']) && ! empty($data['items'])) {
            $order->items()->attach($data['items']);
        }
        if (! empty($data['customer_name']) && ! empty($data['customer_phone'])) {
            $customerInfo = [
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone'],
                'cords' => [
                    'long' => $data['dest_long'],
                    'lat' => $data['dest_lat'],
                ]
            ];
            $customer = (new CustomerServices())->createIfNotExists($customerInfo);
            $order->customer()->associate($customer);
        }
        $order->createCode('pickup', 4);
        $order->createCode('delivered', 4);
        return $order;
    }

    private function chackIfValidBranchWithLocation(Branch|null $branch, Producer $producer): void
    {
        $this->Truthy($branch === null, 'branch is required');
        $this->Truthy($branch->producer_id !== $producer->id, 'invalid operation');
        $this->Truthy($branch->location === null, 'branch location is required');
    }
}
