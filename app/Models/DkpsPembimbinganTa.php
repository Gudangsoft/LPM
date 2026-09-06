<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPembimbinganTa extends Model
{
    protected $table = 'dkps_pembimbingan_ta';

    protected $fillable = [
        'dkps_submission_id',
        'dosen_tetap_id',
        'jml_bimbing_ps_sendiri_ts2',
        'jml_bimbing_ps_sendiri_ts1',
        'jml_bimbing_ps_sendiri_ts',
        'jml_bimbing_ps_lain_ts2',
        'jml_bimbing_ps_lain_ts1',
        'jml_bimbing_ps_lain_ts',
        'jml_pertemuan_ts2',
        'jml_pertemuan_ts1',
        'jml_pertemuan_ts',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function dosenTetap(): BelongsTo
    {
        return $this->belongsTo(DosenTetap::class, 'dosen_tetap_id');
    }
}
