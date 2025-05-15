<?php

declare(strict_types=1);

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BadgeChecks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->badge === null) {
            return response()->json([
                'success' => false,
                'message' => 'missing badge',
            ], 403);
        }

        if ($request->user()->badge->is_valid === 0) {
            return response()->json([
                'success' => false,
                'message' => 'invalid badge',
            ], 403);
        }

        return $next($request);
    }
}
