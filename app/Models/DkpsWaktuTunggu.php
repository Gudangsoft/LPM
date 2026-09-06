<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsWaktuTunggu extends Model
{
    protected $table = 'dkps_waktu_tunggu';

    protected $fillable = [
        'dkps_submission_id',
        'tahun_lulus',
        'jumlah_lulusan',
        'jumlah_terlacak',
        'wt_kurang_6_bulan',
        'wt_6_sampai_12_bulan',
        'wt_lebih_12_bulan',
    ];

    const TAHUN_LULUS = ['TS-4', 'TS-3', 'TS-2'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
