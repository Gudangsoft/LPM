<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanAmi extends Model
{
    use HasFactory;
    use RecordsAuditTrail;

    protected $table = 'penugasan_ami';

    protected $fillable = [
        'jadwal_ami_id',
        'auditor_id',
        'peran',
        'status',
        'catatan',
    ];

    /**
     * Get jadwal AMI
     */
    public function jadwalAmi(): BelongsTo
    {
        return $this->belongsTo(JadwalAmi::class);
    }

    /**
     * Get auditor
     */
    public function auditor(): BelongsTo
    {
        return $this->belongsTo(Auditor::class);
    }

    /**
     * Check if this is ketua
     */
    public function isKetua(): bool
    {
        return $this->peran === 'ketua';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'ditugaskan' => 'info',
            'diterima' => 'success',
            'ditolak' => 'danger',
            'selesai' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Get peran badge color
     */
    public function getPeranColorAttribute(): string
    {
        return $this->peran === 'ketua' ? 'primary' : 'secondary';
    }

    /**
     * Accept assignment
     */
    public function accept(): void
    {
        $this->update(['status' => 'diterima']);
    }

    /**
     * Reject assignment
     */
    public function reject(string $catatan = null): void
    {
        $this->update([
            'status' => 'ditolak',
            'catatan' => $catatan,
        ]);
    }

    /**
     * Mark as complete
     */
    public function complete(): void
    {
        $this->update(['status' => 'selesai']);
    }
}
