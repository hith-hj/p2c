<?php

declare(strict_types=1);

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserChecks
{
    public function handle(Request $request, Closure $next): Response
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
            ], 403);
        }

        return $next($request);
    }
}
