<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auditor extends Model
{
    use HasFactory;

    protected $table = 'auditor';

    protected $fillable = [
        'user_id',
        'nip',
        'no_sertifikat',
        'tanggal_sertifikat',
        'masa_berlaku',
        'bidang_keahlian',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sertifikat' => 'date',
        'masa_berlaku' => 'date',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get penugasan
     */
    public function penugasan(): HasMany
    {
        return $this->hasMany(PenugasanAmi::class);
    }

    /**
     * Get temuan
     */
    public function temuan(): HasMany
    {
        return $this->hasMany(TemuanAmi::class);
    }

    /**
     * Check if certificate is valid
     */
    public function isCertificateValid(): bool
    {
        if (!$this->masa_berlaku) return true;
        return $this->masa_berlaku->isFuture();
    }

    /**
     * Check if auditor is active and can be assigned
     */
    public function canBeAssigned(): bool
    {
        return $this->status === 'aktif' && $this->isCertificateValid();
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status === 'aktif' ? 'success' : 'danger';
    }

    /**
     * Scope for active auditors
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope for available auditors (can be assigned)
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'aktif')
                     ->where(function ($q) {
                         $q->whereNull('masa_berlaku')
                           ->orWhere('masa_berlaku', '>', now());
                     });
    }

    /**
     * Check if certification is expiring within 6 months
     */
    public function isCertExpiringSoon(): bool
    {
        return $this->masa_berlaku
            && $this->masa_berlaku->isFuture()
            && $this->masa_berlaku->lte(now()->addMonths(6));
    }

    /**
     * Scope for auditors whose certification expires within 6 months
     */
    public function scopeCertExpiringSoon($query)
    {
        return $query->where('masa_berlaku', '<=', now()->addMonths(6))
                     ->where('masa_berlaku', '>', now());
    }

    /**
     * Get name via user relation
     */
    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '-';
    }
}
