<?php

namespace App\Models\Concerns;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Content-visibility RBAC (CLAUDE.md §1).
 *
 * A user may see a content row only when ALL three axes hold:
 *   1. department matches the user's department  OR  the item is in a global dept;
 *   2. the user's role rank  >=  the item's min_role;
 *   3. the user's level may see the item's audience_level
 *      (Fresher: onboarding + general; Senior/Admin: also advanced).
 *
 * Admins bypass all three (full access). The host model must expose the columns
 * department_id, min_role, audience_level and a department() relation.
 *
 * Usage:  Article::visibleTo($user)->latest()->paginate();
 */
trait VisibleToUser
{
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        // No authenticated user → nothing is visible (defence in depth).
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        $user->loadMissing('role', 'department');
        $rank = $user->role?->rank ?? Role::RANK_FRESHER;

        // Admin sees everything.
        if ($rank >= Role::RANK_ADMIN) {
            return $query;
        }

        $table = $this->getTable();

        // Axis 1 — department matches, or the item lives in a global department.
        $query->where(function (Builder $q) use ($user, $table) {
            $q->whereHas('department', fn (Builder $d) => $d->where('is_global', true))
                ->orWhere("{$table}.department_id", $user->department_id);
        });

        // Axis 2 — role rank is high enough for the item.
        $query->where("{$table}.min_role", '<=', $rank);

        // Axis 3 — the user's level may see this audience_level.
        $allowedLevels = $rank >= Role::RANK_SENIOR
            ? ['onboarding', 'general', 'advanced']
            : ['onboarding', 'general'];

        $query->whereIn("{$table}.audience_level", $allowedLevels);

        return $query;
    }
}
