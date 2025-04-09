<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Actions\OrderActions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use Illuminate\Http\Request;

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

    public function create(Request $request)
    {
    }
    public function update(Request $request)
    {
    }
    public function delete(Request $request)
    {
    }

    public function accept(Request $request)
    {
    }
    public function reject(Request $request)
    {
    }
    public function cancel(Request $request)
    {
    }
}
