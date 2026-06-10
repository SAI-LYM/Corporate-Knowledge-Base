<?php

namespace App\Policies;

use App\Models\User;

/**
 * Authorization for the Admin user-management screens (CLAUDE.md §3).
 * Managing people (assigning role + department) is an Admin-only capability —
 * the admin routes are already role:Admin gated, and this Policy is the
 * defence-in-depth check on every write action.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isAdmin();
    }
}
