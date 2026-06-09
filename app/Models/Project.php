<?php

namespace App\Models;

use App\Models\Concerns\VisibleToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use VisibleToUser; // same content-visibility RBAC as Article (CLAUDE.md §1)

    protected $fillable = [
        'name',
        'slug',
        'repo_url',
        'tech_stack',
        'status',
        'readme_markdown',
        'owner_id',
        'department_id',
        'audience_level',
        'min_role',
    ];

    protected $casts = [
        'min_role' => 'integer',
        'view_count' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ---- Relationships -----------------------------------------------------

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
