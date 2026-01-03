<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAgent
{
    /**
     * Handle an incoming request.
     * Ensure the authenticated user is an agent.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAgent()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. This endpoint is for field agents only.',
            ], 403);
        }

        if (!$request->user()->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated.',
            ], 403);
        }

        return $next($request);
    }
}
