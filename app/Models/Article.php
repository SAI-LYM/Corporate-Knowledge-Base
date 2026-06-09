<?php

namespace App\Models;

use App\Models\Concerns\VisibleToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use VisibleToUser; // provides scopeVisibleTo() — see CLAUDE.md §1

    /** Allowed audience levels (matches the enum column + validation). */
    public const AUDIENCE_LEVELS = ['onboarding', 'general', 'advanced'];

    protected $fillable = [
        'title',
        'slug',
        'body_markdown',
        'department_id',
        'category_id',
        'audience_level',
        'min_role',
        'author_id',
    ];

    protected $casts = [
        'min_role' => 'integer',
        'view_count' => 'integer',
    ];

    /** Pretty URLs (/knowledge/{slug}); visibleTo() still guards row access. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ---- Relationships -----------------------------------------------------

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
