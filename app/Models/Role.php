<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /** Role names (stable identifiers used in policies/middleware). */
    public const FRESHER = 'Fresher';

    public const SENIOR = 'Senior';

    public const ADMIN = 'Admin';

    /** Seniority ranks for "role >= min_role" comparisons (CLAUDE.md §1). */
    public const RANK_FRESHER = 1;

    public const RANK_SENIOR = 2;

    public const RANK_ADMIN = 3;

    protected $fillable = [
        'name',
        'label',
        'rank',
    ];

    protected $casts = [
        'rank' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
