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
use App\OrderCostServices;
use App\OrderDteCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OrderServices
{
    use ExceptionHandler;
    use OrderCostServices;
    use OrderDteCalculator;

    public function all(
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = []
    ): Collection {
        $orders = Order::query()
            ->with(['attrs', 'items', 'producer', 'carrier', 'transportation', 'branch'])
            ->where('status', OrderStatus::pending->value);
        $this->applyFilters($orders, $filters, ['delivery_type' => OrderDeliveryTypes::values()]);
        $this->applyOrderBy($orders, $orderBy, ['cost', 'distance', 'weight']);
        $orders->paginate(perPage: $perPage, page: $page);
        $orders = $orders->get();
        throw_if($orders->isEmpty(), 'Exception', __('main.orders'));

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
        $orders->with(['attrs', 'items', 'producer', 'carrier', 'transportation', 'branch']);
        $this->applyFilters($orders, $filters, ['status' => OrderStatus::values()]);
        $this->applyOrderBy($orders, $orderBy, ['cost', 'distance', 'weight']);
        $orders->paginate(perPage: $perPage, page: $page);
        $orders = $orders->get();
        throw_if($orders->isEmpty(), 'Exception', __('main.orders'));

        return $orders;
    }

    public function find(int $id): Order
    {
        $order = Order::where('id', $id)->first();
        throw_if($order === null, 'Exception', __('main.order'));

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
        $transportation = (new TransportationServices())->getMatchedTransportation($data['weight']);
        $distance = $this->calcDistance(
            src: ['lat' => $branch->location->lat, 'long' => $branch->location->long],
            dest: ['lat' => $data['dest_lat'], 'long' => $data['dest_long']]
        );
        $distanceInMeter = $distance * 1000;
        $this->checkIfValidDistance($distanceInMeter);
        $inital = $this->initalCost($transportation, $data['weight'], $distance);
        $delivery = $this->deliveryTypeCost($data['delivery_type']);
        $attrs = $this->AttrsCost($data);
        $final = $this->finalCost($inital, $attrs, $delivery);
        // todo : use transportation type to imporve dte
        $dte = $this->Dte([
            'created_at' => now(),
            'delivery_type' => $data['delivery_type'],
            'distance' => $distanceInMeter,
        ]);

        return [
            'distance:m' => $distanceInMeter,
            'weight' => $data['weight'],
            'inital' => $inital,
            'delivery' => $delivery,
            'attrs' => $attrs,
            'final' => $final,
            'dte' => $dte,
        ];
    }

    public function create(Producer $producer, array $data): Order
    {
        $data = $this->checkAndCastData($data, [
            'branch_id' => 'int',
            'delivery_type' => 'string',
            'customer_name' => 'string',
            'goods_price' => 'int',
            'dest_long' => 'float',
            'dest_lat' => 'float',
            'distance' => 'int',
            'weight' => 'int',
            'cost' => 'int',
        ]);
        $branch = $producer->branches()->find($data['branch_id']);
        $this->chackIfValidBranchWithLocation($branch, $producer);
        $transportation = (new TransportationServices())->getMatchedTransportation($data['weight']);
        $order = $producer->orders()->create([
            'branch_id' => $branch->id,
            'src_long' => $branch->location->long,
            'src_lat' => $branch->location->lat,
            'transportation_id' => $transportation->id,
            'delivery_type' => $data['delivery_type'],
            'customer_name' => $data['customer_name'],
            'goods_price' => $data['goods_price'],
            'dest_long' => $data['dest_long'],
            'dest_lat' => $data['dest_lat'],
            'distance' => $data['distance'],
            'weight' => $data['weight'],
            'cost' => $data['cost'],
            'note' => $data['note'] ?? null,
        ]);
        $this->attachRelations($order, $data);
        $order->storeDte();
        $order->createCode('pickup', 4);
        $order->createCode('delivered', 4);

        return $order;
    }

    public function accept(Carrier $carrier, int $order_id): Order
    {
        $order = $this->find($order_id);
        throw_if($order === null, 'Exception', __('main.Not found'));
        throw_if($order->carrier_id !== null, 'Exception', __('main.order is assigned'));
        throw_if($order->status !== OrderStatus::pending->value, 'Exception', __('main.invalid order status'));
        throw_if(
            $order->transportation_id !== $carrier->transportation_id,
            'Exception',
            __('main.transportations is not matched')
        );
        $order->carrier()->associate($carrier);
        $order->update(['status' => OrderStatus::assigned->value]);

        return $order;
    }

    public function picked(Carrier $carrier, int $order_id): Order
    {
        $order = $carrier->orders()->find($order_id);
        throw_if($order === null, 'Exception', __('main.Not found'));
        throw_if($order->status !== OrderStatus::assigned->value, 'Exception', __('main.invalid order status'));
        $order->update([
            'status' => OrderStatus::picked->value,
            'picked_at' => now()->toDateTimeString(),
        ]);

        return $order;
    }

    public function delivered(Carrier $carrier, int $order_id, int $code): Order
    {
        $order = $carrier->orders()->find($order_id);
        throw_if($order === null, 'Exception', __('main.Not found'));
        throw_if($order->status !== OrderStatus::picked->value, 'Exception', __('main.invalid order status'));
        throw_if($order->code('delivered')->code !== $code, 'Exception', __('main.invalid code'));
        $order->update([
            'status' => OrderStatus::delivered->value,
            'delivered_at' => now()->toDateTimeString(),
        ]);

        return $order;
    }

    public function finish(Producer $producer, int $order_id): Order
    {
        $order = $producer->orders()->find($order_id);
        throw_if($order === null, 'Exception', __('main.Not found'));
        throw_if($order->status !== OrderStatus::delivered->value, 'Exception', __('main.invalid order status'));
        $order->update(['status' => OrderStatus::finished->value]);
        $order->codes()->delete();
        $order->createFee($order->carrier);

        return $order;
    }

    public function cancel(Producer $producer, int $order_id): Order
    {
        $order = $producer->orders()->find($order_id);
        throw_if($order === null, 'Exception', __('main.Not found'));
        throw_if($order->carrier_id !== null, 'Exception', __('main.order is assigned'));
        throw_if($order->status !== OrderStatus::pending->value, 'Exception', __('main.invalid order status'));
        $order->update(['status' => OrderStatus::canceld->value]);
        $order->codes()->delete();

        return $order;
    }

    public function forceCancel(Producer $producer, int $order_id): Order
    {
        $order = $producer->orders()->find($order_id);
        throw_if($order === null, 'Exception', __('main.Not found'));
        throw_if($order->status !== OrderStatus::assigned->value, 'Exception', __('main.invalid order status'));
        $order->update(['status' => OrderStatus::canceld->value]);
        $order->codes()->delete();
        $order->createFee($producer);

        return $order;
    }

    public function reject(Carrier $carrier, int $order_id): Order
    {
        $order = $carrier->orders()->find($order_id);
        throw_if($order === null, 'Exception', __('main.Not found'));
        throw_if($order->status !== OrderStatus::assigned->value, 'Exception', __('main.invalid order status'));
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
        throw_if(empty($data), 'Exception', __('main.data is empty'));
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = array_diff(array_keys($requiredFields), array_keys($data));
        throw_if(! empty($missing), 'Exception', __('main.fields missing').implode(', ', $missing));
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

        return $order;
    }

    private function checkIfValidDistance(int $distanceInMeter, int $minRange = 300, int $maxRange = 50000): void
    {
        throw_if(
            $distanceInMeter < $minRange || $distanceInMeter > $maxRange,
            'Exception',
            __("main.Distance should be between 200 and 50000 meter, your is : $distanceInMeter")
        );

    }

    private function chackIfValidBranchWithLocation(Branch $branch, Producer $producer): void
    {
        throw_if($branch === null, 'Exception', __('main.branch is required'));
        throw_if($branch->producer_id !== $producer->id, 'Exception', __('main.invalid operation'));
        throw_if($branch->location === null, 'Exception', __('main.branch location is required'));

    }
}
