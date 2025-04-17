<?php

declare(strict_types=1);

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\User;
use App\OrderServices;

class OrderActions
{
    use ExceptionHandler;
    use OrderServices;

    public function all(int $id)
    {
        $this->Required($id, __('main.user').' ID');
        $user = User::find($id);
        $this->NotFound($user, __('main.user'));
        $this->NotFound($user->orders, __('main.orders'));

        return $user->orders;
    }

    public function create(object $user, array $data)
    {
        $this->Required($user, __('main.user'));
        $this->Required($data, __('main.data'));
        $producer = $user->badge;
        $this->Required($producer, __('main.producer'));
        $branch = $producer->branches()->find($data['branch_id']);
        $this->Required($branch, __('main.branch'));
        $this->Required($branch->location, __('main.branch location'));
        if ($data['delivery_type'] === 'urgent') {
            $data['cost'] = $this->addPercent($data['cost'], 15);
        }

        $order = $user->orders()->create([
            'branch_id' => $branch->id,
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

        if (isset($data['attrs']) && ! empty($data['attrs'])) {
            $order->attrs()->attach($data['attrs']);
        }

        if (isset($data['items']) && ! empty($data['items'])) {
            $order->items()->attach($data['items']);
        }

        return $order;
    }

    public function calcCost(int $branch_id, array $dest, int $weight, array $attrs, string $delivery_type): array
    {
        $this->Required($branch_id, __('main.branch'));
        $this->Required($weight, __('main.weight'));
        $this->Required($dest, __('main.destination coords'));
        $this->Required($delivery_type, __('main.delivery type'));
        $weight = (int) round($weight);
        $trans = (new TransportationActions())->getMatchedTransportation($weight);

        $branch = (new BranchActions())->find($branch_id);
        $src = ['lat' => $branch->location->lat, 'long' => $branch->location->long];
        $distance = $this->calcDistance($src, $dest);
        if ($distance < 100) {
            throw new \Exception(__('main.distance must be greater than 100 meter'));
        }

        $init = $this->initalCost($trans, $weight, $distance);
        $round = (int) round($init);
        $attrs = $this->AttrsCost($attrs);
        $delivery = $this->deliveryTypeCost($delivery_type);
        $final = $this->finalCost($round, $attrs, $delivery);

        return [
            'distance:m' => $distance * 1000,
            'weight:kg' => $weight,
            'init' => $init,
            'round' => $round,
            'attrs' => $attrs,
            'delivery' => $delivery,
            'final' => $final,
        ];
    }
}
