<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKepuasanPenggunaKemampuan extends Model
{
    protected $table = 'dkps_kepuasan_pengguna_kemampuan';

    protected $fillable = [
        'dkps_submission_id',
        'jenis_kemampuan',
        'persen_sangat_baik',
        'persen_baik',
        'persen_cukup',
        'persen_kurang',
        'rencana_tindak_lanjut',
        'urutan',
    ];

    const JENIS_KEMAMPUAN = [
        'etika', 'keahlian_bidang_ilmu', 'bahasa_asing', 'ti', 'komunikasi',
        'kerjasama_tim', 'pengembangan_diri', 'berfikir_kritis', 'kreatifitas',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
