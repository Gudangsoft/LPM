<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPembimbinganMagang extends Model
{
    protected $table = 'dkps_pembimbingan_magang';

    protected $fillable = [
        'dkps_submission_id',
        'dosen_tetap_id',
        'jml_mhs_ts2',
        'jml_mhs_ts1',
        'jml_mhs_ts',
        'jml_pertemuan_ts2',
        'jml_pertemuan_ts1',
        'jml_pertemuan_ts',
        'lama_bulan',
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
