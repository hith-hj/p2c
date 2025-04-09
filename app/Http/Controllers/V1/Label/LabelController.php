<?php

namespace App\Http\Controllers\V1\Label;

use App\Http\Controllers\Controller;

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
}
