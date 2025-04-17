<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Actions\OrderActions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(private readonly OrderActions $order) {}

    public function all(Request $request)
    {
        try {
            return $this->success(payload: [
                'orders' => OrderResource::collection($this->order->all(auth()->id())),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function checkCost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'delivery_type' => ['required', 'string', 'in:normal,urgent'],
            'weight' => ['required', 'numeric', 'min:1'],

            'coords' => ['required', 'array', 'size:2'],
            'coords.long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'coords.lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],

            'attrs' => ['sometimes', 'array'],
            'attrs.*' => ['required', 'exists:attrs,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            return $this->success(
                payload: [
                    'receipt' => $this->order->calcCost(
                        $validator->safe()->input('branch_id'),
                        $validator->safe()->input('coords'),
                        $validator->safe()->input('weight'),
                        $validator->safe()->input('attrs'),
                        $validator->safe()->input('delivery_type'),
                    ),
                ]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'customer_name' => ['required', 'string', 'max:30'],
            'delivery_type' => ['required', 'in:normal,urgent'],
            'goods_price' => ['required', 'numeric'],
            'dest_long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'dest_lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'weight' => ['required', 'numeric', 'min:1'],
            'distance' => ['required', 'numeric', 'min:200'],
            'cost' => ['required', 'numeric'],

            'attrs' => ['sometimes', 'array'],
            'attrs.*' => ['required', 'exists:attrs,id'],

            'items' => ['sometimes', 'array'],
            'items.*' => ['required', 'exists:items,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            return $this->success(
                msg: __('main.created'),
                payload: [
                    'order' => $this->order->create(
                        auth()->user(),
                        $validator->safe()->all()
                    ),
                ]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function delete(Request $request) {}

    public function accept(Request $request) {}

    public function cancel(Request $request) {}
}
