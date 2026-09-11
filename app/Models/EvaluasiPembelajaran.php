<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiPembelajaran extends Model
{
    use RecordsAuditTrail;

    protected $table = 'evaluasi_pembelajaran';

    protected $fillable = [
        'prodi_id',
        'mata_kuliah',
        'dosen_pengampu',
        'tahun_akademik',
        'semester',
        'kesesuaian_rps',
        'kendala',
        'rekomendasi',
        'status',
        'dievaluasi_oleh',
        'dievaluasi_pada',
    ];

    protected $casts = [
        'dievaluasi_pada' => 'datetime',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dievaluasi_oleh');
    }

    public function scopeOwnedByKaprodi($query, User $user)
    {
        return $query->whereHas('prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'dievaluasi' ? 'success' : 'secondary';
    }

    public function getKesesuaianRpsLabelAttribute(): string
    {
        return match ($this->kesesuaian_rps) {
            'sesuai' => 'Sesuai',
            'kurang_sesuai' => 'Kurang Sesuai',
            'tidak_sesuai' => 'Tidak Sesuai',
            default => '-',
        };
    }
}
