<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Order;

use App\Enums\OrderDeliveryTypes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\OrderServices;
use App\Http\Resources\V1\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private readonly OrderServices $order) {}

    public function all(Request $request): JsonResponse
    {
        try {
            $page = $request->integer('page') === 0 ? 1 : $request->integer('page');
            $perPage = $request->integer('perPage') === 0 ? 10 : $request->integer('perPage');
            $filters = $request->filled('filters') ? $request->array('filters') : [];
            $orderBy = $request->filled('orderBy') ? $request->array('orderBy') : [];
            // todo : get orders based on carrier location
            // volume = width * height* length (cubic volume)

            $orders = $this->order->all(
                $page,
                $perPage,
                $filters,
                $orderBy
            );

            return $this->success(payload: [
                'page' => $page,
                'perPage' => $perPage,
                'orders' => OrderResource::collection($orders),
            ]);
        } catch (\Throwable $th) {
            // return $this->error(msg: $th->getMessage());
            return $this->success(payload: ['orders' => []]);
        }
    }

    public function get(Request $request): JsonResponse
    {
        try {
            $page = $request->integer('page') === 0 ? 1 : $request->integer('page');
            $perPage = $request->integer('perPage') === 0 ? 10 : $request->integer('perPage');
            $filters = $request->filled('filters') ? $request->array('filters') : [];
            $orderBy = $request->filled('orderBy') ? $request->array('orderBy') : [];

            $orders = $this->order->get(
                Auth::user()->badge,
                $page,
                $perPage,
                $filters,
                $orderBy
            );

            return $this->success(payload: [
                'page' => $page,
                'perPage' => $perPage,
                'orders' => OrderResource::collection($orders),
            ]);
        } catch (\Throwable $th) {
            return $this->success(payload: ['orders' => []]);
        }
    }

    public function find(Request $request): JsonResponse
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

    public function checkCost(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'delivery_type' => ['required', 'string', Rule::in(OrderDeliveryTypes::cases())],
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
                        Auth::user()->badge,
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
            return $this->error(msg: $th->getMessage());
        }
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'customer_name' => ['required', 'string', 'max:30'],
            'delivery_type' => ['required', Rule::in(OrderDeliveryTypes::cases())],
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
                Auth::user()->badge,
                $validator->safe()->all()
            );

            return $this->success(
                msg: __('main.created'),
                payload: [
                    'order' => OrderResource::make($order->fresh()),
                ]
            );
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function accept(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->accept(
                Auth::user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(msg: __('main.accepted'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function picked(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->picked(
                Auth::user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(
                msg: __('main.picked'),
            );
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function finish(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->finish(
                Auth::user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(msg: __('main.finished'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function delivered(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
            'code' => ['required', 'exists:codes,code'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->delivered(
                Auth::user()->badge,
                $validator->safe()->integer('order_id'),
                $validator->safe()->integer('code'),
            );

            return $this->success(msg: __('main.delivered'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->cancel(
                Auth::user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(msg: __('main.canceled'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function forceCancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->forceCancel(
                Auth::user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(msg: __('main.canceled'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }

    public function reject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => [$validator->errors()]]);
        }

        try {
            $order = $this->order->reject(
                Auth::user()->badge,
                $validator->safe()->integer('order_id')
            );

            return $this->success(msg: __('main.rejected'));
        } catch (\Throwable $th) {
            return $this->error(msg: $th->getMessage());
        }
    }
}
