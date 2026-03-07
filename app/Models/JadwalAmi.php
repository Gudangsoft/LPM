<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalAmi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_ami';

    protected $fillable = [
        'periode_ami_id',
        'prodi_id',
        'tanggal_audit',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_audit' => 'date',
    ];

    /**
     * Get periode AMI
     */
    public function periodeAmi(): BelongsTo
    {
        return $this->belongsTo(PeriodeAmi::class);
    }

    /**
     * Get prodi
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Get penugasan auditor
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
     * Get ketua auditor
     */
    public function ketuaAuditor()
    {
        return $this->penugasan()->where('peran', 'ketua')->first()?->auditor;
    }

    /**
     * Get all assigned auditors
     */
    public function getAuditorsAttribute()
    {
        return $this->penugasan()->with('auditor.user')->get()->pluck('auditor');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'terjadwal' => 'info',
            'berlangsung' => 'warning',
            'selesai' => 'success',
            'ditunda' => 'secondary',
            'batal' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Check if audit is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->tanggal_audit->isFuture() && $this->status === 'terjadwal';
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for upcoming audits
     */
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_audit', '>=', now())
                     ->where('status', 'terjadwal')
                     ->orderBy('tanggal_audit');
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return "{$this->waktu_mulai} - {$this->waktu_selesai}";
    }

    /**
     * Get total temuan count
     */
    public function getTotalTemuanAttribute(): int
    {
        return $this->temuan()->count();
    }

    /**
     * Get open temuan count
     */
    public function getOpenTemuanAttribute(): int
    {
        return $this->temuan()->where('status', 'open')->count();
    }
}
