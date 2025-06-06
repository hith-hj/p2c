<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\V1\Attr;
use App\Models\V1\Item;
use Illuminate\Http\JsonResponse;

final class LabelController extends Controller
{
    public function carBrands(): JsonResponse
    {
        return Success(payload: [
            'Kia', 'Hyundai', 'Ford', 'Mazda', 'Bmw', 'Mercedes',
        ]);
    }

    public function carColors(): JsonResponse
    {
        return Success(payload: [
            'red', 'blue', 'gray', 'black', 'white', 'silver',
        ]);
    }

    public function items(): JsonResponse
    {
        return Success(payload: ['items' => Item::all()]);
    }

    public function attrs(): JsonResponse
    {
        return Success(payload: ['attrs' => Attr::all()]);
    }
}
