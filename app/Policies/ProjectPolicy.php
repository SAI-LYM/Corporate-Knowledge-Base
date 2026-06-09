<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

/**
 * Per-model authorization for Projects (CLAUDE.md §3). Mirrors ArticlePolicy:
 * reads go through the visibleTo() scope; registering/editing is Senior+, with
 * edit/delete limited to the Senior owner or any Admin.
 */
class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // the index is filtered by Project::visibleTo($user)
    }

    public function view(User $user, Project $project): bool
    {
        return Project::whereKey($project->getKey())->visibleTo($user)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isSenior(); // Senior or Admin
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin()
            || ($user->isSenior() && $project->owner_id === $user->id);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}
