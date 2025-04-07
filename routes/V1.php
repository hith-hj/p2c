<?php

use App\Http\Controllers\V1\Auth\JWTAuthController;
use App\Http\Controllers\V1\Branch\BranchController;
use App\Http\Controllers\V1\Carrier\CarrierController;
use App\Http\Controllers\V1\Label\LabelController;
use App\Http\Controllers\V1\Producer\ProducerController;
use App\Http\Controllers\V1\Transportation\TransportationController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\V1\Auth\JwtMiddleware;

Route::group(
    ['prefix' => 'auth', 'controller' => JWTAuthController::class],
    function () {
        Route::withoutMiddleware([JwtMiddleware::class])->group(function () {
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

Route::withoutMiddleware(JwtMiddleware::class)->group(function () {
    Route::group(
        ['prefix' => 'label', 'controller' => LabelController::class],
        function () {
            Route::get('carBrands', 'carBrands');
            Route::get('carColors', 'carColors');
        }
    );
});

Route::group(
    ['prefix' => 'producer', 'controller' => ProducerController::class],
    function () {
        Route::middleware([RoleMiddleware::class.':producer'])->group(function () {
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
    function () {
        Route::middleware([RoleMiddleware::class.':producer'])->group(function () {
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
    function () {
        Route::middleware([RoleMiddleware::class.':carrier'])->group(function () {
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
    function () {
        // Route::middleware([RoleMiddleware::class . ':carrier'])->group(function () {
        //     Route::get('/', 'get');
        //     Route::post('create', 'create');
        //     Route::patch('update', 'update');
        //     Route::delete('delete', 'delete');
        // });

        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);
