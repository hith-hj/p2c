<?php

declare(strict_types=1);

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\BranchController;
use App\Http\Controllers\V1\CarrierController;
use App\Http\Controllers\V1\FeeController;
use App\Http\Controllers\V1\LabelController;
use App\Http\Controllers\V1\NotificationController;
use App\Http\Controllers\V1\OrderController;
use App\Http\Controllers\V1\ProducerController;
use App\Http\Controllers\V1\ReviewController;
use App\Http\Controllers\V1\TransportationController;
use App\Http\Middleware\V1\Auth\JwtMiddleware;
use App\Http\Middleware\V1\BadgeChecks;
use App\Http\Middleware\V1\RoleChecks;
use App\Http\Middleware\V1\UserChecks;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'auth',
        'controller' => AuthController::class,
    ],
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
        [
            'prefix' => 'label',
            'controller' => LabelController::class,
        ],
        function (): void {
            Route::get('carBrands', 'carBrands');
            Route::get('carColors', 'carColors');
            Route::get('items', 'items');
            Route::get('attrs', 'attrs');
        }
    );
});

Route::group(
    [
        'prefix' => 'producer',
        'controller' => ProducerController::class,
        'middleware' => [UserChecks::class],
    ],
    function (): void {
        Route::middleware([RoleChecks::class.':producer'])->group(function (): void {
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
    [
        'prefix' => 'branch',
        'controller' => BranchController::class,
        'middleware' => [UserChecks::class],
    ],
    function (): void {
        Route::middleware([RoleChecks::class.':producer', BadgeChecks::class])
            ->group(function (): void {
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
    [
        'prefix' => 'carrier',
        'controller' => CarrierController::class,
        'middleware' => [UserChecks::class],
    ],
    function (): void {
        Route::middleware([RoleChecks::class.':carrier'])->group(function (): void {
            Route::get('/', 'get');
            Route::post('create', 'create');
            Route::post('createDetails', 'createDetails');
            Route::post('createImages', 'createImages');
            Route::post('setLocation', 'setLocation');
            Route::patch('update', 'update');
            Route::delete('delete', 'delete');
        });

        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);

Route::group(
    [
        'prefix' => 'transportation',
        'controller' => TransportationController::class,
        'middleware' => [UserChecks::class],
    ],
    function (): void {
        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);

Route::group(
    [
        'prefix' => 'order',
        'controller' => OrderController::class,
        'middleware' => [UserChecks::class, BadgeChecks::class],
    ],
    function (): void {
        Route::middleware([RoleChecks::class.':producer'])
            ->group(function (): void {
                Route::post('checkCost', 'checkCost');
                Route::post('create', 'create');
                Route::post('cancel', 'cancel');
                Route::post('forceCancel', 'forceCancel');
                Route::post('finish', 'finish');
            });
        Route::middleware([RoleChecks::class.':carrier'])
            ->group(function (): void {
                Route::get('all', 'all');
                Route::post('accept', 'accept');
                Route::post('reject', 'reject');
                Route::post('picked', 'picked');
                Route::post('delivered', 'delivered');
            });

        Route::get('/', 'get');
        Route::get('find', 'find');
    }
);

Route::group(
    [
        'controller' => FeeController::class,
        'middleware' => [UserChecks::class, BadgeChecks::class],
        'prefix' => 'fee',
    ],
    function (): void {
        Route::get('all', 'all');
        Route::get('find', 'find');
    }
);

Route::group(
    [
        'prefix' => 'notification',
        'controller' => NotificationController::class,
        'middleware' => [UserChecks::class],
    ],
    function (): void {
        Route::get('all', 'all');
        Route::get('find', 'find');
        Route::post('view', 'view');
        Route::post('delete', 'delete');
        Route::get('clear', 'clear');
    }
);

Route::group(
    [
        'prefix' => 'review',
        'controller' => ReviewController::class,
        'middleware' => [UserChecks::class, BadgeChecks::class],
    ],
    function (): void {
        Route::get('all', 'all');
        Route::post('create', 'create');
    }
);
