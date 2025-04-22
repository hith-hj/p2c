<?php

declare(strict_types=1);

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserChecks
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role, bool $isValid): Response
    {
        if ($request->user() === null) {
            return response()->json([
                'success' => false,
                'error' => 'You need to login',
            ], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'error' => 'Un Authorized action',
            ], 403);
        }

        if ($request->user()->verified_at === null) {
            return response()->json([
                'success' => false,
                'error' => 'Unverified account',
            ], 401);
        }

        if (
            $isValid &&
            (! $request->user()->badge || $request->user()->badge->is_valid === false)
        ) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid account',
            ], 401);
        }

        return $next($request);
    }
}
