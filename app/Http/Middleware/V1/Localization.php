<?php

declare(strict_types=1);

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = config('app.locale', 'en');
        if ($request->hasHeader('Accept-Language')) {

            $local = $request->header('Accept-Language');

        } elseif ($request->hasHeader('X-Language')) {

            $local = $request->header('X-Language');

        } else {

            $local = $defaultLocale;
        }

        if (! in_array($local, ['en', 'ar'])) {
            $local = $defaultLocale;
        }

        app()->setLocale($local);

        return $next($request);
    }
}
