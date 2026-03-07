<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    protected $fillable = [
        'kode',
        'nama',
        'jenjang',
        'fakultas',
        'kaprodi_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the kaprodi (head of study program)
     */
    public function kaprodi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kaprodi_id');
    }

    /**
     * Get accreditations for this prodi
     */
    public function akreditasi(): HasMany
    {
        return $this->hasMany(Akreditasi::class);
    }

    /**
     * Get AMI schedules for this prodi
     */
    public function jadwalAmi(): HasMany
    {
        return $this->hasMany(JadwalAmi::class);
    }

    /**
     * Get the latest accreditation
     */
    public function latestAkreditasi()
    {
        return $this->hasOne(Akreditasi::class)->latestOfMany();
    }

    /**
     * Get active accreditation
     */
    public function activeAkreditasi()
    {
        return $this->hasOne(Akreditasi::class)->where('status', 'aktif')->latestOfMany();
    }

    /**
     * Scope for active prodi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full name with jenjang
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->jenjang} - {$this->nama}";
    }
}
