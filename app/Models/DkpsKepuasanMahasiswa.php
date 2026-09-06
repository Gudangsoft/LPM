<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKepuasanMahasiswa extends Model
{
    protected $table = 'dkps_kepuasan_mahasiswa';

    protected $fillable = [
        'dkps_submission_id',
        'aspek',
        'persen_sangat_baik',
        'persen_baik',
        'persen_cukup',
        'persen_kurang',
        'rencana_tindak_lanjut',
    ];

    const ASPEK = ['keandalan', 'daya_tanggap', 'kepastian', 'empati', 'tangible'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
