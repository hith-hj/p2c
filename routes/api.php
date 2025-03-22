<?php

use App\Http\Middleware\V1\Auth\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => JwtMiddleware::class], function () {
    require_once 'V1.php';
});
