<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKurikulum extends Model
{
    protected $table = 'dkps_kurikulum';

    protected $fillable = [
        'dkps_submission_id',
        'semester',
        'kode_mk',
        'nama_mk',
        'kompetensi_inti',
        'sks_kuliah',
        'sks_praktikum',
        'sks_praktik_lapangan',
        'tautan_rps',
        'tautan_asesmen_cpl',
        'urutan',
    ];

    protected $casts = [
        'kompetensi_inti' => 'boolean',
    ];

    const SEMESTER = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function getTotalSksAttribute(): float
    {
        return (float) ($this->sks_kuliah + $this->sks_praktikum + $this->sks_praktik_lapangan);
    }
}
