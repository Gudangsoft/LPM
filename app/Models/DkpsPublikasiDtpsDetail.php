<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPublikasiDtpsDetail extends Model
{
    protected $table = 'dkps_publikasi_dtps_detail';

    protected $fillable = [
        'dkps_submission_id',
        'dosen_tetap_id',
        'judul_artikel',
        'nama_penulis',
        'penulis_peran',
        'jenis_jurnal',
        'terindeks',
        'tanggal_terbit',
        'urutan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
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
