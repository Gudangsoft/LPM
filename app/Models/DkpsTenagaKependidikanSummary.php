<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsTenagaKependidikanSummary extends Model
{
    protected $table = 'dkps_tenaga_kependidikan_summary';

    protected $fillable = [
        'dkps_submission_id',
        'jenis',
        'jumlah_s3',
        'jumlah_s2',
        'jumlah_s1',
        'jumlah_d4',
        'jumlah_d3',
        'jumlah_sma_smk',
        'unit_kerja',
    ];

    const JENIS = ['pustakawan', 'laboran', 'administrasi', 'lainnya'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function getJumlahTotalAttribute(): int
    {
        return (int) ($this->jumlah_s3 + $this->jumlah_s2 + $this->jumlah_s1
            + $this->jumlah_d4 + $this->jumlah_d3 + $this->jumlah_sma_smk);
    }
}
