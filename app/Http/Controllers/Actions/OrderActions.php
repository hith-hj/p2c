<?php

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\Attr;
use App\Models\V1\Producer;
use App\Models\V1\User;

class OrderActions
{
    use ExceptionHandler;

    public function __construct()
    {
    }

    public function all(?int $id = null)
    {
        $this->Required($id, __('main.user') . ' ID');
        $user = User::find($id);
        $this->NotFound($user, __('main.user'));
        $this->NotFound($user->orders, __('main.orders'));

        return $user->orders;
    }

    public function create(array $data)
    {
        $this->Required($data, __('main.data'));
        $producer = Producer::findOrFail($data['producer_id']);
        $producer->orders->create([
            "producer_id" => $data[''],
            "branch_id" => $data[''],
            "customer_name" => $data[''],
            "delivery_type" => $data[''],
            "goods_price" => $data[''],
            "src_long" => $data[''],
            "src_lat" => $data[''],
            "dist_long" => $data[''],
            "dist_lat" => $data[''],
            "distance" => $data[''],
            "weight" => $data[''],
            "cost" => $data['']
        ]);
    }

    public function calcCost($coords, $weight, $attrs)
    {
        $this->Required($weight, __('main.weight'));
        $this->Required($coords, __('main.coords'));

        $distance = $this->calcDistance($coords);
        $trans = (new TransportationActions)->getMatchedTransportation($weight);

        $init = $this->initalCost($trans, $weight, $distance);
        $rounded = ceil($init / 10) * 10;
        $extra = $this->getExtraCostForAttributes($attrs);
        $final = $this->finalCost($rounded, $extra);

        return [
            $distance,
            $init,
            $rounded,
            $extra,
            $final
        ];
    }
    private function calcDistance(array $coords)
    {
        $src = $coords['src'];
        $dest = $coords['dest'];
        $rad = M_PI / 180;
        return
            round(
                acos(
                    sin($src['lat'] * $rad) * sin($dest['lat'] * $rad) +
                    cos($src['lat'] * $rad) * cos($dest['lat'] * $rad) *
                    cos($src['long'] * $rad - $dest['long'] * $rad)
                ) * 6371
            ,2);// Kilometers
    }  

    private function initalCost($trans, $weight, $distance)
    {
        return
            $trans->inital_cost +
            $weight * $trans->cost_per_kg +
            $distance * 1000 * $trans->cost_per_km;
    }

    private function finalCost($cost, $extra)
    {
        $final = $cost;
        if (is_array($extra)) {
            foreach ($extra as $value) {
                $final = $this->addPercent($final, $value);
            }
        } else {
            if ($extra !== 0) {
                $final = $this->addPercent($final, $extra);
            }
        }
        return floor($final);
    }

    private function getExtraCostForAttributes($attrs, $calcType = 'totla')
    {
        if (empty($attrs)) {
            return 0;
        }
        $query = Attr::whereIn('id', $attrs);
        return match ($calcType) {
            default => $query->sum('extra_cost_percent'),
            'total' => $query->sum('extra_cost_percent'),
            'byone' => $query->pluck('extra_cost_percent')->toArray(),
        };
    }

    private function addPercent($number, $percent)
    {
        return $number * (1 + $percent / 100);
    }

}
