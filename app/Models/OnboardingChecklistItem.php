<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingChecklistItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'position',
        'department_id',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserChecklistProgress::class, 'checklist_item_id');
    }

    /**
     * Items a given user should see: global items (no department) plus the
     * items specific to their department, in display order.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query
            ->where(function (Builder $q) use ($user) {
                $q->whereNull('department_id')
                    ->orWhere('department_id', $user->department_id);
            })
            ->orderBy('position');
    }
}
