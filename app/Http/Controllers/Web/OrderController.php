<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Services\OrderServices;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class OrderController extends Controller
{
    public function __construct(private readonly OrderServices $order) {}

    public function get(Request $request, string $serial): View
    {
        $order = $this->order->findBy('serial', $serial)->only($this->orderDataToShow());

        return view('public.order.details', ['order' => $order]);
    }

    private function orderDataToShow()
    {
        return [
            'weight',
            'cost',
            'delivery_type',
            'goods_price',
            'distance',
            'note',
            'created_at',
            'status',
        ];
    }
}
