<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemuanAmi extends Model
{
    use HasFactory;

    protected $table = 'temuan_ami';

    protected $fillable = [
        'jadwal_ami_id',
        'auditor_id',
        'standar',
        'standar_mutu_id',
        'kategori',
        'deskripsi',
        'bukti',
        'bukti_file',
        'akar_masalah',
        'rekomendasi',
        'batas_tindak_lanjut',
        'status',
    ];

    protected $casts = [
        'batas_tindak_lanjut' => 'date',
    ];

    /**
     * Get jadwal AMI
     */
    public function jadwalAmi(): BelongsTo
    {
        return $this->belongsTo(JadwalAmi::class);
    }

    /**
     * Get the auditor who found this
     */
    public function auditor(): BelongsTo
    {
        return $this->belongsTo(Auditor::class);
    }

    public function standarMutu(): BelongsTo
    {
        return $this->belongsTo(StandarMutu::class);
    }

    /**
     * Get tindak lanjut
     */
    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class);
    }

    /**
     * Get latest tindak lanjut
     */
    public function latestTindakLanjut()
    {
        return $this->hasOne(TindakLanjut::class)->latestOfMany();
    }

    /**
     * Get kategori badge color
     */
    public function getKategoriColorAttribute(): string
    {
        return match($this->kategori) {
            'mayor' => 'danger',
            'minor' => 'warning',
            'observasi' => 'info',
            'rekomendasi' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'closed' => 'success',
            'verified' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Check if temuan is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->batas_tindak_lanjut) return false;
        return $this->batas_tindak_lanjut->isPast() && !in_array($this->status, ['closed', 'verified']);
    }

    /**
     * Get days until deadline
     */
    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->batas_tindak_lanjut) return null;
        return now()->diffInDays($this->batas_tindak_lanjut, false);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for open temuan
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Scope for overdue temuan
     */
    public function scopeOverdue($query)
    {
        return $query->where('batas_tindak_lanjut', '<', now())
                     ->whereNotIn('status', ['closed', 'verified']);
    }

    /**
     * Scope by kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope to only records belonging to a prodi the given user heads (kaprodi_id).
     */
    public function scopeOwnedByKaprodi($query, User $user)
    {
        return $query->whereHas('jadwalAmi.prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
    }

    /**
     * Get prodi via jadwal
     */
    public function getProdiAttribute()
    {
        return $this->jadwalAmi?->prodi;
    }

    /**
     * Close temuan
     */
    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * Verify temuan
     */
    public function verify(): void
    {
        $this->update(['status' => 'verified']);
    }
}
