<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChecklistProgress extends Model
{
    protected $table = 'user_checklist_progress';

    protected $fillable = [
        'user_id',
        'checklist_item_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OnboardingChecklistItem::class, 'checklist_item_id');
    }
}
