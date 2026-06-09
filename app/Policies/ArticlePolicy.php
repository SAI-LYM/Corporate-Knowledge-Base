<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

/**
 * Per-model authorization for Articles (CLAUDE.md §3 — writes gated by a Policy).
 *
 * Reads: view() reuses the visibleTo() scope so a single item is governed by the
 * exact same RBAC as the listing — a Fresher cannot open an advanced/other-dept
 * article by guessing its URL.
 * Writes: authoring is Senior+; edit/delete is the Senior author or any Admin.
 */
class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // the index is filtered by Article::visibleTo($user)
    }

    public function view(User $user, Article $article): bool
    {
        return Article::whereKey($article->getKey())->visibleTo($user)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isSenior(); // Senior or Admin
    }

    public function update(User $user, Article $article): bool
    {
        return $user->isAdmin()
            || ($user->isSenior() && $article->author_id === $user->id);
    }

    public function delete(User $user, Article $article): bool
    {
        return $this->update($user, $article);
    }
}
