<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use DB;
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

            $currentPath    = strtolower(trim($request->path(), '/'));
            $currentSegment = strtolower($request->segment(1) ?? '');
            $allModules = DB::table('menu_modules')
                ->whereNotNull('slug')
                ->pluck('slug')
                ->map(fn($v) => strtolower(trim($v, '/')))
                ->toArray();
            $allGroups = DB::table('menu_groups')
                ->whereNotNull('slug')
                ->pluck('slug')
                ->map(fn($v) => strtolower(trim($v, '/')))
                ->toArray();
            $isModuleInDB = false;
            foreach ($allModules as $module) {
                if ($currentPath === $module || str_starts_with($currentPath, $module . '/')) {
                    $isModuleInDB = true;
                    break;
                }
            }
        
            $isGroupInDB = in_array($currentSegment, $allGroups, true);
            if (!$isModuleInDB && !$isGroupInDB) {
                return $next($request);
            }
            $allowedModules = DB::table('menu_permissions as mp')
                ->join('menu_modules as mm', 'mm.id', '=', 'mp.menu_module_id')
                ->where('mp.user_id', $user->id)
                ->whereNotNull('mm.slug')
                ->pluck('mm.slug')
                ->map(fn($v) => strtolower(trim($v, '/')))
                ->toArray();
            $allowedGroups = DB::table('menu_permissions as mp')
                ->join('menu_groups as mg', 'mg.id', '=', 'mp.menu_group_id')
                ->where('mp.user_id', $user->id)
                ->whereNotNull('mg.slug')
                ->pluck('mg.slug')
                ->map(fn($v) => strtolower(trim($v, '/')))
                ->toArray();
            $hasModuleAccess = false;
            foreach ($allowedModules as $module) {
                if ($currentPath === $module || str_starts_with($currentPath, $module . '/')) {
                    $hasModuleAccess = true;
                    break;
                }
            }
        
            $hasGroupAccess = in_array($currentSegment, $allowedGroups, true);
            if (!$hasModuleAccess && !$hasGroupAccess) {
                abort(403, 'Unauthorized');
            }
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
