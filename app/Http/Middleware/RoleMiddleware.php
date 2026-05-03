<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login dan role sesuai
        if (!$request->user() || $request->user()->role !== $role) {
            return response()->json([
                'message' => 'Unauthorized. Anda tidak memiliki akses ke fitur ini.'
            ], 403);
        }

        return $next($request);
    }
}