<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsRekognisiDtps extends Model
{
    protected $table = 'dkps_rekognisi_dtps';

    protected $fillable = [
        'dkps_submission_id',
        'dosen_tetap_id',
        'bidang_keahlian',
        'deskripsi_rekognisi',
        'jenis_rekognisi',
        'tahun',
        'tingkat',
        'bukti_file',
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
