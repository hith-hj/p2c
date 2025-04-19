<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\Enums\OrderStatus;
use App\ExceptionHandler;
use App\Models\V1\Carrier;
use App\Models\V1\Order;
use App\Models\V1\User;
use App\OrderServices;
use Date;

class OrderActions
{
    use ExceptionHandler;
    use OrderServices;

    public function all()
    {
        $orders = Order::all();
        $this->NotFound($orders, __('main.orders'));

        return $orders;
    }


    public function get(int $id)
    {
        $this->Required($id, __('main.user') . ' ID');
        $user = User::find($id);
        $this->NotFound($user, __('main.user'));
        $this->NotFound($user->orders, __('main.orders'));

        return $user->orders;
    }

    public function find(int $id)
    {
        $this->Required($id, __('main.order') . ' ID');
        $order = Order::find($id);
        $this->NotFound($order, __('main.order'));

        return $order;
    }

    public function create(object $producer, array $data)
    {
        $this->Required($data, __('main.data'));
        $this->Required($producer, __('main.producer'));
        $branch = $producer->branches()->find($data['branch_id']);
        $this->Required($branch, __('main.branch'));
        if ($branch->producer_id !== $producer->id) {
            throw new \Exception(__('main.invalid operation'));
        }
        $this->Required($branch->location, __('main.branch location'));
        if ($data['delivery_type'] === 'urgent') {
            $data['cost'] = $this->addPercent($data['cost'], 15);
        }
        $trans = (new TransportationActions())->getMatchedTransportation($data['weight']);

        $order = $producer->orders()->create([
            'branch_id' => $branch->id,
            'transportation_id' => $trans->id,
            'customer_name' => $data['customer_name'],
            'delivery_type' => $data['delivery_type'],
            'goods_price' => $data['goods_price'],
            'src_long' => $branch->location->long,
            'src_lat' => $branch->location->lat,
            'dest_long' => $data['dest_long'],
            'dest_lat' => $data['dest_lat'],
            'distance' => $data['distance'],
            'weight' => $data['weight'],
            'cost' => $data['cost'],
        ]);

        if (isset($data['attrs']) && !empty($data['attrs'])) {
            $order->attrs()->attach($data['attrs']);
        }

        if (isset($data['items']) && !empty($data['items'])) {
            $order->items()->attach($data['items']);
        }

        return $order;
    }

    public function calcCost(
        int $weight,
        int $branch_id,
        float $dest_long,
        float $dest_lat,
        string $delivery_type,
        array $attrs
    ): array {

        $this->Required($weight, __('main.weight'));
        $this->Required($branch_id, __('main.branch'));
        $this->Required($dest_long, __('main.destination coords'));
        $this->Required($dest_lat, __('main.destination coords'));
        $this->Required($delivery_type, __('main.delivery type'));

        $weight = (int) round($weight);
        $trans = (new TransportationActions())->getMatchedTransportation($weight);

        $branch = (new BranchActions())->find($branch_id);
        $src = ['lat' => $branch->location->lat, 'long' => $branch->location->long,];
        $dest = ['lat' => $dest_lat, 'long' => $dest_long,];
        $distance = $this->calcDistance($src, $dest);
        if ($distance < 100) {
            throw new \Exception(__('main.distance must be greater than 100 meter'));
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
            'attrs' => $attrs,
            'delivery' => $delivery,
            'final' => $final,
        ];
    }

    public function cancel(int $order_id)
    {
        $order = $this->find($order_id);
        if ($order->carrier_id !== null) {
            throw new \Exception(__('main.order is assigned'));
        }
        if ($order->status !== OrderStatus::pending->value) {
            throw new \Exception(__('main.order is not pending'));
        }
        return $order->update(['status'=>OrderStatus::canceld]);
    }

    public function accept(object $carrier, int $order_id)
    {
        $order = $this->find($order_id);
        if ($order->carrier_id !== null) {
            throw new \Exception(__('main.order is assigned'));
        }
        if ($order->status !== OrderStatus::pending->value) {
            throw new \Exception(__('main.order status invalid'));
        }
        if ($order->transportation_id !== $carrier->transportation_id) {
            throw new \Exception(__('main.transportations is not matched'));
        }
        return $order->update([
            'carrier_id'=>$carrier->id,
            'status'=>OrderStatus::assigned->value
        ]);
    }

    public function reject(object $carrier, int $order_id)
    {
        $order = $this->find($order_id);
        if ($order->carrier_id !== $carrier->id) {
            throw new \Exception(__('main.order is not yours'));
        }
        if ($order->status !== OrderStatus::assigned->value) {
            throw new \Exception(__('main.order status is not valid'));
        }
        return $order->update([ 'status'=>OrderStatus::rejected->value ]);
    }

    public function picked(object $carrier, int $order_id)
    {
        $order = $this->find($order_id);
        if ($order->carrier_id !== $carrier->id) {
            throw new \Exception(__('main.order is not yours'));
        }
        if ($order->status !== OrderStatus::assigned->value) {
            throw new \Exception(__('main.order status is not valid'));
        }
        return $order->update([ 'status'=>OrderStatus::picked->value, 'picked_at'=> now() ]);
    }

    public function deliverd(object $carrier, int $order_id)
    {
        $order = $this->find($order_id);
        if ($order->carrier_id !== $carrier->id) {
            throw new \Exception(__('main.order is not yours'));
        }
        if ($order->status !== OrderStatus::assigned->value) {
            throw new \Exception(__('main.order status is not valid'));
        }
        return $order->update([ 'status'=>OrderStatus::picked->value, 'picked_at'=> now() ]);
    }

}
