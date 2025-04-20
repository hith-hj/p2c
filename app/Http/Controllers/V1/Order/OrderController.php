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
                'orders' => OrderResource::collection($this->order->all()),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function get(Request $request)
    {
        try {
            return $this->success(payload: [
                'orders' => OrderResource::collection($this->order->get(auth()->user()->badge)),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function find(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->find($validator->safe()->integer('order_id'));

            return $this->success(payload: [
                'order' => OrderResource::make($order),
            ]);
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function checkCost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'delivery_type' => ['required', 'string', 'in:normal,urgent,express'],
            'weight' => ['required', 'numeric', 'min:1'],
            'dest_long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'dest_lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
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
                        auth()->user()->badge,
                        $validator->safe()->integer('weight'),
                        $validator->safe()->integer('branch_id'),
                        $validator->safe()->float('dest_long'),
                        $validator->safe()->float('dest_lat'),
                        $validator->safe()->input('delivery_type'),
                        $validator->safe()->array('attrs'),
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
            'delivery_type' => ['required', 'in:normal,urgent,express'],
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
            $order = $this->order->create(
                auth()->user()->badge,
                $validator->safe()->all()
            );

            return $this->success(
                msg: __('main.created'),
                payload: [
                    'order' => OrderResource::make($order->fresh()),
                ]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function accept(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->accept(
                auth()->user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(
                msg: __('main.accepted'),
                payload: [
                    'pickup_code' => $order->code('pickup')?->code,
                ]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function picked(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->picked(
                auth()->user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(
                msg: __('main.picked'),
                payload: [
                    'delivered_code' => $order->code('delivered')->code,
                ]
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function delivered(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->delivered(
                auth()->user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(
                msg: __('main.delivered'),
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->cancel(
                auth()->user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(
                msg: __('main.canceled'),
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }

    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->reject(
                auth()->user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(
                msg: __('main.rejected'),
            );
        } catch (\Throwable $th) {
            return $this->error(payload: ['errors' => $th->getMessage()]);
        }
    }
}
