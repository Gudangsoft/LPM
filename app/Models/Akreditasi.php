<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Akreditasi extends Model
{
    use HasFactory;

    protected $table = 'akreditasi';

    protected $fillable = [
        'prodi_id',
        'lembaga',
        'peringkat',
        'nomor_sk',
        'tanggal_sk',
        'tanggal_kadaluarsa',
        'file_sk',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_kadaluarsa' => 'date',
    ];

    /**
     * Get the prodi
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Check if akreditasi is expired
     */
    public function isExpired(): bool
    {
        return $this->tanggal_kadaluarsa->isPast();
    }

    /**
     * Check if akreditasi will expire soon (within 6 months)
     */
    public function isExpiringSoon(): bool
    {
        return $this->tanggal_kadaluarsa->isBetween(now(), now()->addMonths(6));
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute(): int
    {
        return now()->diffInDays($this->tanggal_kadaluarsa, false);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'success',
            'kadaluarsa' => 'danger',
            'proses_perpanjangan' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Scope for active akreditasi
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope for expiring soon
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('tanggal_kadaluarsa', '<=', now()->addMonths(6))
                     ->where('tanggal_kadaluarsa', '>', now());
    }

    /**
     * Scope for expired
     */
    public function scopeExpired($query)
    {
        return $query->where('tanggal_kadaluarsa', '<', now());
    }

    /**
     * Update status based on expiration date
     */
    public function updateStatusBasedOnExpiration(): void
    {
        if ($this->isExpired() && $this->status === 'aktif') {
            $this->update(['status' => 'kadaluarsa']);
        }
    }
}
