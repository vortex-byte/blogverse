<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user authenticated with token
        if (!$request->bearerToken() || empty($request->bearerToken()) || !Auth::guard('sanctum')->check()) {
            return response()->json(
                ['status' => 'error', 'message' => 'Unauthenticated.'],
                401
            );
        }

        return $next($request);
    }
}
