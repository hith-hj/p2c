<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Actions\OrderActions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function __construct(private OrderActions $order)
    {
    }

    public function all(Request $request)
    {
        try {
            return $this->success(payload: [
                'orders' => OrderResource::collection($this->order->all(auth()->id())),
            ]);
        } catch (\Throwable $e) {
            return $this->error(msg: $e->getMessage());
        }
    }

    public function checkCost(Request $request){
        $validator = Validator::make($request->all(), [
            'distance' => ['required', 'numeric', 'min:200'],
            'weight' => ['required', 'numeric', 'min:1'],
            'attrs' => ['sometimes', 'array'],
            'attrs.*' => ['required', 'exists:attrs,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            return $this->success(
                payload: ['cost' => $this->order->calcCost(
                    $validator->safe()->input('distance'),
                    $validator->safe()->input('weight'),
                    $validator->safe()->input('attrs'),
                )]
            );
        } catch (\Throwable $e) {
            return $this->error(payload: ['errors' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
    }
    public function delete(Request $request)
    {
    }

    public function accept(Request $request)
    {
    }
    public function cancel(Request $request)
    {
    }
}
