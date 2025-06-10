<?php

declare(strict_types=1);

use App\Http\Controllers\Web\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/public/order/details/{serial}', [OrderController::class, 'get'])->name('order.details');
