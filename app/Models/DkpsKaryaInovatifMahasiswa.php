<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsKaryaInovatifMahasiswa extends Model
{
    protected $table = 'dkps_karya_inovatif_mahasiswa';

    protected $fillable = [
        'dkps_submission_id',
        'kategori',
        'nim',
        'nama_mahasiswa',
        'judul',
        'tahun',
        'keterangan',
        'peringkat_jurnal',
        'tautan',
        'urutan',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
