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
            "delivery_type" => ['required','string','in:normal,urrgent'],
            'weight' => ['required', 'numeric', 'min:1'],

            'coords' => ['required', 'array', 'size:2'],
            'coords.src' => ['required','array','size:2'],

            'coords.src.long' => ['required', 'numeric', 'between:-180,180', 'required_with:src.lat'],
            'coords.src.lat' => ['required', 'numeric', 'between:-90,90', 'required_with:src.long'],

            'coords.dest' => ['required','array','size:2'],
            'coords.dest.long' => ['required', 'numeric', 'between:-180,180', 'required_with:dest.lat'],
            'coords.dest.lat' => ['required', 'numeric', 'between:-90,90', 'required_with:dest.long'],

            'attrs' => ['sometimes', 'array'],
            'attrs.*' => ['required', 'exists:attrs,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            return $this->success(
                payload: ['cost' => $this->order->calcCost(
                    $validator->safe()->input('coords'),
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
