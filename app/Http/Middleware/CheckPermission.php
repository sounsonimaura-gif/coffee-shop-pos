<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Usage in routes:  ->middleware('permission:menu_items.create')
     * Multiple permissions can be passed (separated by | or ,)
     *   ->middleware('permission:menu_items.create|menu_items.update')
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $required = collect($permissions)
            ->flatMap(fn ($p) => preg_split('/[|,]/', $p))
            ->map(fn ($p) => trim($p))
            ->filter()
            ->values();

        if ($required->isEmpty()) {
            return $next($request);
        }

        $userPermissions = $user->permissionNames();

        $hasAny = $required->contains(fn ($perm) => in_array($perm, $userPermissions, true) || in_array('*', $userPermissions, true));

        if (! $hasAny) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                abort(403, __('coffee.no_permission'));
            }
            abort(403, __('coffee.no_permission'));
        }

        return $next($request);
    }
}
