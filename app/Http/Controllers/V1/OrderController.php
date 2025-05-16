<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Enums\OrderDeliveryTypes;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use App\Http\Services\OrderServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private readonly OrderServices $order) {}

    public function all(Request $request): JsonResponse
    {
        $page = $request->filled('page') ? $request->integer('page') : 1;
        $perPage = $request->filled('perPage') ? $request->integer('perPage') : 10;
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

        return Success(payload: [
            'page' => $page,
            'perPage' => $perPage,
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function get(Request $request): JsonResponse
    {
        $page = $request->filled('page') ? $request->integer('page') : 1;
        $perPage = $request->filled('perPage') ? $request->integer('perPage') : 10;
        $filters = $request->filled('filters') ? $request->array('filters') : [];
        $orderBy = $request->filled('orderBy') ? $request->array('orderBy') : [];

        $orders = $this->order->get(
            Auth::user()->badge,
            $page,
            $perPage,
            $filters,
            $orderBy
        );

        return Success(payload: [
            'page' => $page,
            'perPage' => $perPage,
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $order = $this->order->find($validator->safe()->integer('order_id'));

        return Success(payload: ['order' => OrderResource::make($order)]);
    }

    public function checkCost(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'delivery_type' => ['required', 'string', Rule::in(OrderDeliveryTypes::cases())],
            'weight' => ['required', 'numeric', 'min:1', 'max:5000'],
            'dest_long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'dest_lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'attrs' => ['sometimes', 'array', 'max:5'],
            'attrs.*' => ['required', 'exists:attrs,id'],
            'items' => ['sometimes', 'array', 'max:5'],
            'items.*' => ['required', 'exists:items,id'],
        ]);

        $receipt = $this->order->calcCost(Auth::user()->badge, $validator->safe()->all());

        return Success(payload: ['receipt' => $receipt]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => ['required', 'exists:branches,id'],
            'customer_name' => ['required', 'string', 'max:30'],
            'customer_phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/'],
            'delivery_type' => ['required', Rule::in(OrderDeliveryTypes::cases())],
            'dest_long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'dest_lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'distance' => ['required', 'numeric', 'min:200', 'max:50000'],
            'weight' => ['required', 'numeric', 'min:1', 'max:5000'],
            'goods_price' => ['required', 'numeric'],
            'cost' => ['required', 'numeric'],
            'attrs' => ['sometimes', 'array', 'max:5'],
            'attrs.*' => ['required', 'exists:attrs,id'],
            'items' => ['sometimes', 'array', 'max:5'],
            'items.*' => ['required', 'exists:items,id'],
            'note' => ['sometimes', 'string', 'max:200'],
        ]);

        $order = $this->order->create(Auth::user()->badge, $validator->safe()->all());

        return Success(
            msg: __('main.created'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function accept(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $this->order->accept(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(msg: __('main.accepted'));
    }

    public function picked(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $this->order->picked(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(msg: __('main.picked'));
    }

    public function delivered(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
            'code' => ['required', 'exists:codes,code'],
        ]);

        $this->order->delivered(
            Auth::user()->badge,
            $validator->safe()->integer('order_id'),
            $validator->safe()->integer('code'),
        );

        return Success(msg: __('main.delivered'));
    }

    public function finish(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $this->order->finish(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(msg: __('main.finished'));
    }

    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $this->order->cancel(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(msg: __('main.canceled'));
    }

    public function forceCancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
        $this->order->forceCancel(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(msg: __('main.canceled'));
    }

    public function reject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
        $this->order->reject(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(msg: __('main.rejected'));
    }
}
