<?php

declare(strict_types=1);

use App\Http\Middleware\V1\Auth\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => JwtMiddleware::class], function (): void {
    require_once 'V1.php';
});
