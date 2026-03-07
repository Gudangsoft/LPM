<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'role',
        'avatar',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    // =========================================
    // ROLE-BASED ACCESS CONTROL
    // =========================================

    /**
     * Get roles for this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $slug): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $slugs): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($slugs) {
            $query->whereIn('slug', $slugs);
        })->exists();
    }

    /**
     * Get all permissions for this user
     */
    public function getAllPermissions(): array
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('slug')
            ->unique()
            ->toArray();
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(Role $role): void
    {
        $this->roles()->syncWithoutDetaching($role);
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role);
    }

    // =========================================
    // LEGACY ROLE METHODS (for backward compatibility)
    // =========================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isAuditor(): bool
    {
        return $this->hasRole('auditor') || $this->auditor()->exists();
    }

    public function isKaprodi(): bool
    {
        return $this->hasRole('kaprodi') || $this->prodiDikepalai()->exists();
    }

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class);
    }

    /**
     * Get auditor profile if exists
     */
    public function auditor(): HasOne
    {
        return $this->hasOne(Auditor::class);
    }

    /**
     * Get prodi where this user is kaprodi
     */
    public function prodiDikepalai(): HasMany
    {
        return $this->hasMany(Prodi::class, 'kaprodi_id');
    }

    /**
     * Get tindak lanjut submitted by user
     */
    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class);
    }

    /**
     * Get tindak lanjut reviewed by user
     */
    public function reviewedTindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'reviewed_by');
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithRole($query, string $roleSlug)
    {
        return $query->whereHas('roles', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        });
    }

    public function scopeAuditors($query)
    {
        return $query->whereHas('auditor');
    }

    public function scopeKaprodi($query)
    {
        return $query->whereHas('prodiDikepalai');
    }
}
