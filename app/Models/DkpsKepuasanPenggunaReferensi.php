<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKepuasanPenggunaReferensi extends Model
{
    protected $table = 'dkps_kepuasan_pengguna_referensi';

    protected $fillable = [
        'dkps_submission_id',
        'tahun_lulus',
        'jumlah_lulusan',
        'jumlah_tanggapan_terlacak',
    ];

    const TAHUN_LULUS = ['TS-4', 'TS-3', 'TS-2'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
