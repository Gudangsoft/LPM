<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsSitasiDtps extends Model
{
    protected $table = 'dkps_sitasi_dtps';

    protected $fillable = [
        'dkps_submission_id',
        'dosen_tetap_id',
        'judul_karya_disitasi',
        'jumlah_sitasi',
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
