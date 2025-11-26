<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RoleAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // If user not logged in or role is not 1 or 2
        if (!$user || !in_array($user->role, [1, 2])) {
            abort(403, 'Unauthorized');
        }
        if ($user->role == 2) {
            return $next($request);
        }
        $currentRoute = $request->route()?->getName();
        $deniedRoutes = [
            'business.mytasklist',
        ];

        if (in_array($currentRoute, $deniedRoutes)) {
            abort(403, 'Unauthorized');
        }

        $allowedRoutes = [
            'home',
            'profile.view',
            'dashboard',
            'business.*',
            'tabs.*',
            'profile.*',
            'adminapp.getList'
        ];

        // Check if current route matches any allowed pattern
        foreach ($allowedRoutes as $pattern) {
            if (Str::is($pattern, $currentRoute)) {
                return $next($request);
            }
        }

        // Deny access for anything not explicitly allowed
        abort(403, 'Unauthorized');
    }
}
