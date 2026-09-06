<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsIntegrasiPenelitianPkm extends Model
{
    protected $table = 'dkps_integrasi_penelitian_pkm';

    protected $fillable = [
        'dkps_submission_id',
        'dosen_tetap_id',
        'judul_penelitian_pkm',
        'mata_kuliah',
        'bentuk_integrasi',
        'tahun_relatif',
        'bukti_file',
        'urutan',
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
