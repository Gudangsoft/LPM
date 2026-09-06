<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKesesuaianBidang extends Model
{
    protected $table = 'dkps_kesesuaian_bidang';

    protected $fillable = [
        'dkps_submission_id',
        'tahun_lulus',
        'jumlah_lulusan',
        'jumlah_terlacak',
        'rendah',
        'sedang',
        'tinggi',
    ];

    const TAHUN_LULUS = ['TS-4', 'TS-3', 'TS-2'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
