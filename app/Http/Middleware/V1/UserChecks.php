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
    public function handle(Request $request, Closure $next, string $role, bool $isValid = false): Response
    {
        if ($request->user() === null) {
            return response()->json([
                'success' => false,
                'message' => 'You need to login',
            ], 401);
        }

        if ($request->user()->verified_at === null) {
            return response()->json([
                'success' => false,
                'message' => 'Unverified account',
            ], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Un Authorized action',
            ], 403);
        }

        return $next($request);
    }
}
