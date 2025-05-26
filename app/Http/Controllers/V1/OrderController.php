<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use App\Http\Services\OrderServices;
use App\Http\Validators\OrderValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $validator = OrderValidators::find($request->all());

        $order = $this->order->find($validator->safe()->integer('order_id'));

        return Success(payload: ['order' => OrderResource::make($order)]);
    }

    public function checkCost(Request $request): JsonResponse
    {
        $validator = OrderValidators::checkCost($request->all());

        $receipt = $this->order->calcCost(Auth::user()->badge, $validator->safe()->all());

        return Success(payload: ['receipt' => $receipt]);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = OrderValidators::create($request->all());

        $order = $this->order->create(Auth::user()->badge, $validator->safe()->all());

        return Success(
            msg: __('main.created'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function accept(Request $request): JsonResponse
    {
        $validator = OrderValidators::accept($request->all());

        $order = $this->order->accept(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(
            msg: __('main.accepted'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function picked(Request $request): JsonResponse
    {
        $validator = OrderValidators::picked($request->all());

        $order = $this->order->picked(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(
            msg: __('main.picked'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function delivered(Request $request): JsonResponse
    {
        $validator = OrderValidators::delivered($request->all());

        $order = $this->order->delivered(
            Auth::user()->badge,
            $validator->safe()->integer('order_id'),
            $validator->safe()->integer('code'),
        );

        return Success(
            msg: __('main.delivered'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function finish(Request $request): JsonResponse
    {
        $validator = OrderValidators::finish($request->all());

        $order = $this->order->finish(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(
            msg: __('main.finished'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function cancel(Request $request): JsonResponse
    {
        $validator = OrderValidators::cancel($request->all());

        $order = $this->order->cancel(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(
            msg: __('main.canceled'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function forceCancel(Request $request): JsonResponse
    {
        $validator = OrderValidators::forceCancel($request->all());

        $order = $this->order->forceCancel(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(
            msg: __('main.canceled'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }

    public function reject(Request $request): JsonResponse
    {
        $validator = OrderValidators::reject($request->all());

        $order = $this->order->reject(
            Auth::user()->badge,
            $validator->safe()->integer('order_id')
        );

        return Success(
            msg: __('main.rejected'),
            payload: ['order' => OrderResource::make($order->fresh())]
        );
    }
}
