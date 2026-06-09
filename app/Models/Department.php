<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_global',
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(OnboardingChecklistItem::class);
    }

    /** The single global ("Company-wide") department whose content everyone sees. */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->where('is_global', true);
    }
}
