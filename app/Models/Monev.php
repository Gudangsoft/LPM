<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Monev extends Model
{
    use RecordsAuditTrail;

    protected $table = 'monev';

    protected $fillable = [
        'prodi_id',
        'tahun_akademik',
        'semester',
        'aspek_monev',
        'hasil',
        'tanggal_monev',
        'petugas_monev',
        'status',
        'tindak_lanjut',
    ];

    protected $casts = [
        'tanggal_monev' => 'date',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'baik' => 'success',
            'cukup' => 'warning',
            'kurang' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baik' => 'Baik',
            'cukup' => 'Cukup',
            'kurang' => 'Kurang',
            default => ucfirst($this->status),
        };
    }
}
