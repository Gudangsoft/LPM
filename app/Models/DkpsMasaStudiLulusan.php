<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsMasaStudiLulusan extends Model
{
    protected $table = 'dkps_masa_studi_lulusan';

    protected $fillable = [
        'dkps_submission_id',
        'tahun_masuk',
        'jumlah_diterima',
        'lulus_ts7',
        'lulus_ts6',
        'lulus_ts5',
        'lulus_ts4',
        'lulus_ts3',
        'lulus_ts2',
        'lulus_ts1',
        'lulus_ts',
    ];

    const TAHUN_MASUK = ['TS-7', 'TS-6', 'TS-5', 'TS-4', 'TS-3'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
