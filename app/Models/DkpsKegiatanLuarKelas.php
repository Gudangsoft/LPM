<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKegiatanLuarKelas extends Model
{
    protected $table = 'dkps_kegiatan_luar_kelas';

    protected $fillable = [
        'dkps_submission_id',
        'nama_tema_kegiatan',
        'dosen_pembina',
        'tanggal',
        'tahun_relatif',
        'bukti_file',
        'urutan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
