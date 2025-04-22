<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Label;

use App\Http\Controllers\Controller;
use App\Models\V1\Attr;
use App\Models\V1\Item;
use Illuminate\Http\JsonResponse;

class LabelController extends Controller
{
    public function carBrands(): JsonResponse
    {
        return $this->success(payload: [
            'Kia', 'Hyundai', 'Ford', 'Mazda', 'Bmw', 'Mercedes',
        ]);
    }

    public function carColors(): JsonResponse
    {
        return $this->success(payload: [
            'red', 'blue', 'gray', 'black', 'white', 'silver',
        ]);
    }

    public function items(): JsonResponse
    {
        return $this->success(payload: ['items' => Item::all()]);
    }

    public function attrs(): JsonResponse
    {
        return $this->success(payload: ['attrs' => Attr::all()]);
    }
}
