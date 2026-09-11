<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapaianPembelajaran extends Model
{
    use RecordsAuditTrail;

    protected $table = 'capaian_pembelajaran';

    protected $fillable = [
        'prodi_id',
        'mata_kuliah',
        'cpmk',
        'deskripsi_cpmk',
        'tahun_akademik',
        'semester',
        'target_capaian',
        'realisasi_capaian',
        'status',
        'catatan',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function scopeOwnedByKaprodi($query, User $user)
    {
        return $query->whereHas('prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'tercapai' => 'success',
            'tidak_tercapai' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'tercapai' => 'Tercapai',
            'tidak_tercapai' => 'Tidak Tercapai',
            default => 'Belum Dievaluasi',
        };
    }
}
