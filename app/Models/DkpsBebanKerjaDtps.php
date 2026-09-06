<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsBebanKerjaDtps extends Model
{
    protected $table = 'dkps_beban_kerja_dtps';

    protected $fillable = [
        'dkps_submission_id',
        'dosen_tetap_id',
        'sks_pendidikan_ps',
        'sks_pendidikan_ps_lain_dalam',
        'sks_pendidikan_ps_lain_luar',
        'sks_penelitian',
        'sks_pkm',
        'sks_tugas_tambahan',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function dosenTetap(): BelongsTo
    {
        return $this->belongsTo(DosenTetap::class, 'dosen_tetap_id');
    }

    public function getJumlahSksAttribute(): float
    {
        return (float) ($this->sks_pendidikan_ps + $this->sks_pendidikan_ps_lain_dalam
            + $this->sks_pendidikan_ps_lain_luar + $this->sks_penelitian
            + $this->sks_pkm + $this->sks_tugas_tambahan);
    }
}
