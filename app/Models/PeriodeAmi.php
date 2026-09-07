<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeAmi extends Model
{
    use HasFactory;
    use RecordsAuditTrail;

    protected $table = 'periode_ami';

    protected $fillable = [
        'nama',
        'tahun_akademik',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get all jadwal AMI for this period
     */
    public function jadwalAmi(): HasMany
    {
        return $this->hasMany(JadwalAmi::class);
    }

    /**
     * Check if period is active
     */
    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'aktif' => 'success',
            'selesai' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Scope for active periods
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Get total jadwal count
     */
    public function getTotalJadwalAttribute(): int
    {
        return $this->jadwalAmi()->count();
    }

    /**
     * Get completed jadwal count
     */
    public function getCompletedJadwalAttribute(): int
    {
        return $this->jadwalAmi()->where('status', 'selesai')->count();
    }

    /**
     * Get progress percentage
     */
    public function getProgressAttribute(): float
    {
        $total = $this->total_jadwal;
        if ($total === 0) return 0;
        
        return round(($this->completed_jadwal / $total) * 100, 1);
    }
}
