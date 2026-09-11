<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SasaranMutu extends Model
{
    use RecordsAuditTrail;

    protected $table = 'sasaran_mutu';

    protected $fillable = [
        'standar_mutu_id',
        'prodi_id',
        'tahun_akademik',
        'uraian_sasaran',
        'indikator',
        'target',
        'satuan',
        'realisasi',
        'status',
        'keterangan',
    ];

    public function standarMutu(): BelongsTo
    {
        return $this->belongsTo(StandarMutu::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
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
