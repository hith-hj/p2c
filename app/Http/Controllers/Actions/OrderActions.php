<?php

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\Attr;
use App\Models\V1\User;

class OrderActions
{
    use ExceptionHandler;

    public function __construct() {}

    public function all(?int $id = null)
    {
        $this->Required($id, __('main.user').' ID' );
        $user = User::find($id);
        $this->NotFound($user, __('main.user') );
        $this->NotFound($user->orders, __('main.orders') );

        return $user->orders;
    }

    public function calcCost($weight,$distance,$attrs){
        $this->Required($weight, __('main.weight'));
        $this->Required($distance, __('main.distance'));

        $trans = (new TransportationActions)->getMatchedTransportation($weight);

        $init = $this->initalCost($trans,$weight,$distance);
        $rounded = ceil($init /10) * 10;
        $extra = $this->getExtraCostForAttributes($attrs);
        $final = $this->finalCost($rounded,$extra);
        
        return [
            $init,
            $rounded,
            $extra,
            $final
        ];
    }

    private function initalCost($trans,$weight,$distance){
        return 
        $trans->inital_cost + 
        $weight * $trans->cost_per_kg + 
        $distance/1000 * $trans->cost_per_km ;
    }

    private function finalCost($cost,$extra){
        $final = $cost;
        if(is_array($extra)){
            foreach ($extra as $value) {
                $final = $this->addPercent($final,$value);
            }
        }else{
            if($extra !== 0){
                $final = $this->addPercent($final,$extra);
            }
        }
        return floor($final);
    }

    private function getExtraCostForAttributes($attrs,$calcType = 'totla'){
        if(empty($attrs)){ return 0; }
        $query = Attr::whereIn('id',$attrs);
        return match($calcType){
            default => $query->sum('extra_cost_percent'),
            'total' => $query->sum('extra_cost_percent'),
            'byone' => $query->pluck('extra_cost_percent')->toArray(),
        };
    }

    private function addPercent($number, $percent){
        return $number * (1 + $percent / 100);
    }

}
