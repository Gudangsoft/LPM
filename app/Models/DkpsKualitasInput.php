<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKualitasInput extends Model
{
    protected $table = 'dkps_kualitas_input';

    protected $fillable = [
        'dkps_submission_id',
        'tahun_relatif',
        'daya_tampung',
        'pendaftar',
        'lulus_seleksi',
        'mahasiswa_baru_reguler',
        'mahasiswa_baru_transfer',
        'mahasiswa_aktif_reguler',
        'mahasiswa_aktif_transfer',
        'mahasiswa_pddikti',
    ];

    const TAHUN_RELATIF = ['TS-4', 'TS-3', 'TS-2', 'TS-1', 'TS'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
