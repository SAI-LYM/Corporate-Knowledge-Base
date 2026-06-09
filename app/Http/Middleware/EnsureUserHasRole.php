<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level RBAC guard (CLAUDE.md §3). Hierarchical: a higher role satisfies a
 * lower requirement (Senior passes `role:Fresher`, Admin passes everything).
 *
 * Usage in routes:  ->middleware('role:Senior')   // Senior or Admin
 *                   ->middleware('role:Admin')     // Admin only
 *
 * Authorisation of specific write actions still goes through Policies; this only
 * gates whole routes by seniority.
 */
class EnsureUserHasRole
{
    /** Map role name → seniority rank. */
    private const RANKS = [
        Role::FRESHER => Role::RANK_FRESHER,
        Role::SENIOR => Role::RANK_SENIOR,
        Role::ADMIN => Role::RANK_ADMIN,
    ];

    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        $required = self::RANKS[$role] ?? Role::RANK_ADMIN;

        if (! $user || $user->roleRank() < $required) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
