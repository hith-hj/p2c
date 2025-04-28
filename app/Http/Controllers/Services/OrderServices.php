<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use App\Enums\OrderDeliveryTypes;
use App\Enums\OrderStatus;
use App\ExceptionHandler;
use App\Models\V1\Carrier;
use App\Models\V1\Order;
use App\Models\V1\Producer;
use App\OrderCostServices;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class OrderServices
{
    use ExceptionHandler;
    use OrderCostServices;

    public function all(int $page = 1, int $perPage = 10, array $filters = [], array $orderBy = []): Paginator
    {
        $orders = Order::query()
            ->with(['attrs', 'items', 'producer', 'carrier', 'transportation', 'branch'])
            ->where('status', OrderStatus::pending->value)
            ->when(! empty($filters), function (Builder $query) use ($filters) {
                $query
                    ->when(isset($filters['delivery_type']), function (Builder $type) use ($filters) {
                        if (in_array($filters['delivery_type'], OrderDeliveryTypes::values())) {
                            $type->where('delivery_type', $filters['delivery_type']);
                        }
                    });
            })
            ->when(! empty($orderBy) && count($orderBy) === 1, function (Builder $query) use ($orderBy) {
                $key = array_key_first($orderBy);
                $value = array_pop($orderBy);
                if (in_array($key, ['cost', 'distance', 'weight']) || in_array($value, ['asc', 'desc'])) {
                    $query->orderBy($key, $value);
                }
            })
            ->simplePaginate(perPage: $perPage, page: $page);
        $this->NotFound($orders, __('main.orders'));

        return $orders;
    }

    public function get(object $badge, int $page, int $perPage, array $filters): Paginator
    {
        $this->Required($badge, __('main.user').' ID');
        $orders = $badge->orders()
            ->with(['attrs', 'items', 'producer', 'carrier', 'transportation', 'branch'])
            ->when(! empty($filters), function (Builder $query) use ($filters) {
                $query->when(isset($filters['status']), function (Builder $status) use ($filters) {
                    if (in_array($filters['status'], OrderStatus::cases())) {
                        $status->where('status', $filters['status']);
                    }
                });
            })
            ->when(! empty($orderBy) && count($orderBy) === 1, function (Builder $query) use ($orderBy) {
                $key = array_key_first($orderBy);
                $value = array_pop($orderBy);
                if (in_array($key, ['cost', 'distance', 'weight']) || in_array($value, ['asc', 'desc'])) {
                    $query->orderBy($key, $value);
                }
            })
            ->simplePaginate(perPage: $perPage, page: $page);
        $this->NotFound($orders, __('main.orders'));

        return $orders;
    }

    public function find(int $id): Order
    {
        $this->Required($id, __('main.order').' ID');
        $order = Order::where('id', $id)->first();
        $this->NotFound($order, __('main.order'));

        return $order;
    }

    public function calcCost(
        Producer $producer,
        int $weight,
        int $branch_id,
        float $dest_long,
        float $dest_lat,
        string $delivery_type,
        array $attrs
    ): array {

        $this->Required($weight, __('main.weight'));
        $this->Required($weight, __('main.producer'));
        $this->Required($branch_id, __('main.branch'));
        $this->Required($dest_long, __('main.longitude'));
        $this->Required($dest_lat, __('main.latitude'));
        $this->Required($delivery_type, __('main.delivery type'));

        $branch = (new BranchServices())->find($branch_id);
        if ($branch->producer_id !== $producer->id) {
            throw new Exception(__('main.invalid operation'));
        }
        $src = ['lat' => $branch->location->lat, 'long' => $branch->location->long];

        $weight = (int) round($weight);
        $trans = (new TransportationServices())->getMatchedTransportation($weight);

        $dest = ['lat' => $dest_lat, 'long' => $dest_long];
        $distance = $this->calcDistance($src, $dest);
        if ($distance < 100) {
            throw new Exception(__('main.Min distance is 100 meter'));
        }

        $init = $this->initalCost($trans, $weight, $distance);
        $rounded = (int) round($init);
        $attrs = $this->AttrsCost($attrs);
        $delivery = $this->deliveryTypeCost($delivery_type);
        $final = $this->finalCost($rounded, $attrs, $delivery);

        return [
            'distance:m' => $distance * 1000,
            'weight:kg' => $weight,
            'rounded' => $rounded,
            'delivery' => $delivery,
            'attrs' => $attrs,
            'final' => $final,
        ];
    }

    public function create(Producer $producer, array $data): Order
    {
        $this->Required($producer, __('main.producer'));
        $this->Required($data, __('main.data'));
        $branch = $producer->branches()->find($data['branch_id']);
        $this->Required($branch, __('main.branch'));
        if ($branch->producer_id !== $producer->id) {
            throw new Exception(__('main.invalid operation'));
        }
        $this->Required($branch->location, __('main.branch location'));

        $trans = (new TransportationServices())->getMatchedTransportation($data['weight']);

        $data['transportation_id'] = $trans->id;
        $data['src_lat'] = $branch->location->lat;
        $data['src_long'] = $branch->location->long;
        $data['delivery_type'] = OrderDeliveryTypes::from($data['delivery_type']);

        $order = $producer->orders()->create([
            'branch_id' => $data['branch_id'],
            'transportation_id' => $data['transportation_id'],
            'customer_name' => $data['customer_name'],
            'delivery_type' => $data['delivery_type'],
            'goods_price' => $data['goods_price'],
            'src_long' => $data['src_long'],
            'src_lat' => $data['src_lat'],
            'dest_long' => $data['dest_long'],
            'dest_lat' => $data['dest_lat'],
            'distance' => $data['distance'],
            'weight' => $data['weight'],
            'cost' => $data['cost'],
        ]);

        if (isset($data['attrs']) && ! empty($data['attrs'])) {
            $order->attrs()->attach($data['attrs']);
        }

        if (isset($data['items']) && ! empty($data['items'])) {
            $order->items()->attach($data['items']);
        }
        $order->createCode('pickup', 4);
        $order->createCode('delivered', 4);

        return $order;
    }

    public function accept(Carrier $carrier, int $order_id): Order
    {
        $this->Required($carrier, __('main.carrier'));
        $order = $this->find($order_id);
        if ($order->carrier_id !== null) {
            throw new Exception(__('main.order is assigned'));
        }
        if ($order->status !== OrderStatus::pending->value) {
            throw new Exception(__('main.invalid order status'));
        }
        if ($order->transportation_id !== $carrier->transportation_id) {
            throw new Exception(__('main.transportations is not matched'));
        }
        $order->carrier()->associate($carrier);
        $order->update(['status' => OrderStatus::assigned->value]);

        return $order;
    }

    public function picked(Carrier $carrier, int $order_id): Order
    {
        $this->Required($carrier, __('main.carrier'));
        $order = $carrier->orders()->find($order_id);
        if (! $order) {
            throw new Exception(__('main.not found'));
        }
        if ($order->status !== OrderStatus::assigned->value) {
            throw new Exception(__('main.invalid order status'));
        }
        $order->update(['status' => OrderStatus::picked->value, 'picked_at' => now()]);

        return $order;
    }

    public function delivered(Carrier $carrier, int $order_id, int $code): Order
    {
        $this->Required($carrier, __('main.carrier'));
        $order = $carrier->orders()->find($order_id);
        if (! $order) {
            throw new Exception(__('main.not found'));
        }
        if ($order->status !== OrderStatus::picked->value) {
            throw new Exception(__('main.invalid order status'));
        }
        if ($order->code('delivered')->code !== $code) {
            throw new Exception(__('main.invalid code'));
        }
        $order->update(['status' => OrderStatus::delivered->value, 'delivered_at' => now()]);
        $order->codes()->delete();

        return $order;
    }

    public function finish(Producer $producer, int $order_id): Order
    {
        $this->Required($producer, __('main.producer'));
        $order = $producer->orders()->find($order_id);
        if (! $order) {
            throw new Exception(__('main.not found'));
        }
        if ($order->status !== OrderStatus::delivered->value) {
            throw new Exception(__('main.invalid order status'));
        }
        $order->update(['status' => OrderStatus::finished->value]);
        $order->createFee($order->carrier);

        return $order;
    }

    public function cancel(Producer $producer, int $order_id): Order
    {
        $this->Required($producer, __('main.producer'));
        $order = $producer->orders()->find($order_id);
        if (! $order) {
            throw new Exception(__('main.not found'));
        }
        if ($order->carrier_id !== null) {
            throw new Exception(__('main.order is assigned'));
        }
        if ($order->status !== OrderStatus::pending->value) {
            throw new Exception(__('main.invalid order status'));
        }
        $order->update(['status' => OrderStatus::canceld]);
        $order->codes()->delete();

        return $order;
    }

    public function forceCancel(Producer $producer, int $order_id): Order
    {
        $this->Required($producer, __('main.producer'));
        $order = $producer->orders()->find($order_id);
        if (! $order) {
            throw new Exception(__('main.not found'));
        }
        if ($order->status !== OrderStatus::assigned->value) {
            throw new Exception(__('main.invalid order status'));
        }
        $order->update(['status' => OrderStatus::canceld]);
        $order->codes()->delete();
        $order->createFee($producer);

        return $order;
    }

    public function reject(Carrier $carrier, int $order_id): Order
    {
        $this->Required($carrier, __('main.carrier'));
        $order = $carrier->orders()->find($order_id);
        if (! $order) {
            throw new Exception(__('main.not found'));
        }
        if ($order->status !== OrderStatus::assigned->value) {
            throw new Exception(__('main.invalid order status'));
        }
        $order->update(['status' => OrderStatus::rejected->value]);
        $order->codes()->delete();
        $order->createFee($carrier);

        return $order;
    }
}
