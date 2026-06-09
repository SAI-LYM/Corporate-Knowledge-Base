<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'azure_oid',
        'role_id',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---- Relationships -----------------------------------------------------

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** Articles this user authored. */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /** Projects this user owns. */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function checklistProgress(): HasMany
    {
        return $this->hasMany(UserChecklistProgress::class);
    }

    /** Checklist items this user has completed (via the progress pivot). */
    public function completedChecklistItems(): BelongsToMany
    {
        return $this->belongsToMany(
            OnboardingChecklistItem::class,
            'user_checklist_progress',
            'user_id',
            'checklist_item_id',
        )->withTimestamps()->withPivot('completed_at');
    }

    // ---- Role helpers (display only — server RBAC lives in Policies/scopes) --

    /** Seniority rank of this user's role (1=Fresher, 2=Senior, 3=Admin). */
    public function roleRank(): int
    {
        return $this->role?->rank ?? Role::RANK_FRESHER;
    }

    public function hasRoleAtLeast(int $rank): bool
    {
        return $this->roleRank() >= $rank;
    }

    public function isFresher(): bool
    {
        return $this->roleRank() === Role::RANK_FRESHER;
    }

    public function isSenior(): bool
    {
        return $this->hasRoleAtLeast(Role::RANK_SENIOR);
    }

    public function isAdmin(): bool
    {
        return $this->hasRoleAtLeast(Role::RANK_ADMIN);
    }
}
