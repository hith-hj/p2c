<?php

declare(strict_types=1);

use App\Http\Middleware\V1\Localization;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(Localization::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e, Request $request) {
            if ($request->is('api/*') && $request->wantsJson()) {
                return match (true) {
                    $e instanceof AuthenticationException => Error(
                        msg: 'Unauthorized Request.',
                        code: Response::HTTP_UNAUTHORIZED
                    ),

                    $e instanceof ValidationException => Error(
                        msg: 'Validation failed.',
                        code: Response::HTTP_UNPROCESSABLE_ENTITY,
                        payload: ['errors' => $e->errors()]
                    ),

                    $e instanceof NotFoundHttpException => Error(
                        msg: 'Resource Not found.',
                        code: Response::HTTP_NOT_FOUND
                    ),

                    $e instanceof ModelNotFoundException => Error(
                        msg: "{$e->getModel()} id {$e->getIds()} Not found.",
                        code: Response::HTTP_NOT_FOUND
                    ),

                    $e instanceof RouteNotFoundException => Error(
                        msg: 'Route not found.',
                        code: Response::HTTP_NOT_FOUND
                    ),

                    $e instanceof HttpException => Error(
                        msg: $e->getMessage(),
                        code: $e->getStatusCode()
                    ),

                    $e instanceof Throwable => Error(msg: 'Error: '.$e->getMessage()),

                    default => Error(msg: 'Error: '.$e->getMessage())
                };
            }
        });
    })->create();
