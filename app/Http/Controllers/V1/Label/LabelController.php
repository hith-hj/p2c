<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Label;

use App\Http\Controllers\Controller;
use App\Models\V1\Attr;
use App\Models\V1\Item;

class LabelController extends Controller
{
    public function carBrands()
    {
        return $this->success(payload: [
            'Kia', 'Hyundai', 'Ford', 'Mazda', 'Bmw', 'Mercedes',
        ]);
    }

    public function carColors()
    {
        return $this->success(payload: [
            'red', 'blue', 'gray', 'black', 'white', 'silver',
        ]);
    }

    public function items()
    {
        return $this->success(payload: ['items' => Item::all()]);
    }

    public function attrs()
    {
        return $this->success(payload: ['attrs' => Attr::all()]);
    }
}
