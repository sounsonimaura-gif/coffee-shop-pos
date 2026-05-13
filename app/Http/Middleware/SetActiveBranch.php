<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures that the user has an active branch selected (or auto-selects
 * their default branch). The active branch id is stored in the session
 * and exposed to the front-end via Inertia shared props.
 */
class SetActiveBranch
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $branchId = session('active_branch_id');

            if (! $branchId) {
                $branchId = $user->default_branch_id
                    ?? optional($user->branches()->first())->id;
            }

            if ($branchId) {
                session(['active_branch_id' => $branchId]);
                $branch = $user->branches()->where('branches.id', $branchId)->first()
                    ?? $user->company?->branches()->find($branchId);
                if ($branch) {
                    session(['active_branch_name' => $branch->name]);
                }
            }
        }

        return $next($request);
    }
}
