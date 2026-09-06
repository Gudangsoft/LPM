<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsIpkLulusan extends Model
{
    protected $table = 'dkps_ipk_lulusan';

    protected $fillable = [
        'dkps_submission_id',
        'tahun_lulus',
        'jumlah_lulusan',
        'ipk_min',
        'ipk_rata',
        'ipk_maks',
    ];

    const TAHUN_LULUS = ['TS-2', 'TS-1', 'TS'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
