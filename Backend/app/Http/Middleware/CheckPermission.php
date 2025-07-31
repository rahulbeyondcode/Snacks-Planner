<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $module, $action, $resource = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null,
                'status' => 401
            ], 401);
        }

        if (!$user->hasPermission($module, $action, $resource)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'data' => null,
                'status' => 403
            ], 403);
        }

        return $next($request);
    }
} 