<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPenelitianPkmMahasiswa extends Model
{
    protected $table = 'dkps_penelitian_pkm_mahasiswa';

    protected $fillable = [
        'dkps_submission_id',
        'jenis',
        'nama_dtps',
        'judul_tema',
        'nim_nama_mahasiswa',
        'peran_mahasiswa',
        'tahun_relatif',
        'urutan',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis);
    }
}
