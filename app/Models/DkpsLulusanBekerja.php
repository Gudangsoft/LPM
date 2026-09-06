<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsLulusanBekerja extends Model
{
    protected $table = 'dkps_lulusan_bekerja';

    protected $fillable = [
        'dkps_submission_id',
        'tahun_lulus',
        'jumlah_lulusan',
        'jumlah_terlacak',
        'bekerja_sesuai_bidang',
        'usaha_mandiri',
        'studi_lanjut_s2',
        'mengikuti_ppg',
    ];

    const TAHUN_LULUS = ['TS-4', 'TS-3', 'TS-2'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
