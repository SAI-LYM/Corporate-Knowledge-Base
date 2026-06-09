<?php

/*
 |----------------------------------------------------------------------------
 | CANONICAL MODEL PATTERN  (reference only — not autoloaded)
 |----------------------------------------------------------------------------
 | Copy this shape for content models (Article, Project). Key points:
 |
 |  - $fillable is explicit (mass-assignment safe — CLAUDE.md §3). Never $guarded = [].
 |  - Relationships are real Eloquent relations, not ad-hoc queries.
 |  - The content-visibility RBAC lives in ONE reusable query scope, visibleTo(),
 |    shared via the VisibleToUser trait — never scattered if-checks (CLAUDE.md §1).
 |  - getRouteKeyName() = 'slug' so URLs are /documents/{slug}, and visibleTo()
 |    still guards row access even on a direct slug/ID.
 |
 | Usage:  Document::visibleTo($user)->latest()->paginate();
 */

namespace App\Models;

use App\Models\Concerns\VisibleToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use VisibleToUser; // provides scopeVisibleTo()

    /** Mass-assignable attributes (CLAUDE.md §3). */
    protected $fillable = [
        'title',
        'slug',
        'body_markdown',
        'audience_level',
        'min_role',
        'department_id',
        'author_id',
    ];

    protected $casts = [
        'min_role'   => 'integer',
        'view_count' => 'integer',
    ];

    /** Pretty, guessable URLs — visibleTo() still enforces access. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ---- Relationships -----------------------------------------------------

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
