<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {        
        Log::info('RoleMiddleware loaded!', ['user' => $request->user()]);
        // ...rest of your code
        $user = $request->user();
        if (!$user || !in_array($user->role->name, $roles)) {
            return response()->json(['message' => 'Forbidden. You do not have the required role.'], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
