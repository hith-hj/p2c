<?php

declare(strict_types=1);

use App\Http\Controllers\V1\Auth\JWTAuthController;
use App\Http\Controllers\V1\Branch\BranchController;
use App\Http\Controllers\V1\Carrier\CarrierController;
use App\Http\Controllers\V1\Label\LabelController;
use App\Http\Controllers\V1\Order\OrderController;
use App\Http\Controllers\V1\Producer\ProducerController;
use App\Http\Controllers\V1\Transportation\TransportationController;
use App\Http\Middleware\V1\Auth\JwtMiddleware;
use App\Http\Middleware\V1\UserChecks;

Route::group(
    ['prefix' => 'auth', 'controller' => JWTAuthController::class],
    function (): void {
        Route::withoutMiddleware([JwtMiddleware::class])->group(function (): void {
            Route::post('register', 'register')->name('register');
            Route::post('verify', 'verify')->name('verify');
            Route::post('login', 'login')->name('login');
            Route::post('forgetPassword', 'forgetPassword')->name('forgetPassword');
            Route::post('resetPassword', 'resetPassword')->name('resetPassword');
            Route::post('resendCode', 'resendCode')->name('resendCode');
            Route::get('refreshToken', 'refreshToken')->name('refreshToken');
        });

        Route::post('deleteUser', 'deleteUser')->name('deleteUser');
        Route::get('user', 'getUser')->name('getUser');
        Route::post('logout', 'logout')->name('logout');
        Route::post('changePassword', 'changePassword')->name('changePassword');
    }
);

Route::withoutMiddleware(JwtMiddleware::class)->group(function (): void {
    Route::group(
        ['prefix' => 'label', 'controller' => LabelController::class],
        function (): void {
            Route::get('carBrands', 'carBrands');
            Route::get('carColors', 'carColors');
            Route::get('items', 'items');
            Route::get('attrs', 'attrs');
        }
    );
});

Route::group(
    ['prefix' => 'producer', 'controller' => ProducerController::class],
    function (): void {
        Route::middleware([UserChecks::class.':producer'])->group(function (): void {
            Route::get('/', 'get');
            Route::post('create', 'create');
            Route::patch('update', 'update');
            Route::delete('delete', 'delete');
        });

        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);

Route::group(
    ['prefix' => 'branch', 'controller' => BranchController::class],
    function (): void {
        Route::middleware([UserChecks::class.':producer'])->group(function (): void {
            Route::get('/', 'get');
            Route::post('create', 'create');
            Route::patch('update', 'update');
            Route::delete('delete', 'delete');
            Route::post('setDefault', 'setDefault');
        });

        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);

Route::group(
    ['prefix' => 'carrier', 'controller' => CarrierController::class],
    function (): void {
        Route::middleware([UserChecks::class.':carrier'])->group(function (): void {
            Route::get('/', 'get');
            Route::post('create', 'create');
            Route::post('createDetails', 'createDetails');
            Route::post('createDocuments', 'createDocuments');
            Route::patch('update', 'update');
            Route::delete('delete', 'delete');
        });

        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);

Route::group(
    ['prefix' => 'transportation', 'controller' => TransportationController::class],
    function (): void {
        // Route::middleware([UserChecks::class . ':carrier'])->group(function () {
        //     Route::get('/', 'get');
        //     Route::post('create', 'create');
        //     Route::patch('update', 'update');
        //     Route::delete('delete', 'delete');
        // });

        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);

Route::group(
    ['prefix' => 'order', 'controller' => OrderController::class],
    function (): void {
        Route::middleware([UserChecks::class.':producer,true'])
            ->group(function (): void {
                Route::post('checkCost', 'checkCost');
                Route::post('create', 'create');
                Route::post('cancel', 'cancel');
                Route::post('forceCancel', 'forceCancel');
                Route::post('finish', 'finish');
            });
        Route::middleware([UserChecks::class.':carrier,true'])
            ->group(function (): void {
                Route::post('accept', 'accept');
                Route::post('reject', 'reject');
                Route::post('picked', 'picked');
                Route::post('delivered', 'delivered');
            });

        Route::get('/', 'get');
        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);
