<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPrestasiMahasiswa extends Model
{
    protected $table = 'dkps_prestasi_mahasiswa';

    protected $fillable = [
        'dkps_submission_id',
        'nama_kegiatan',
        'jenis_prestasi',
        'tanggal_perolehan',
        'tingkat',
        'prestasi_dicapai',
        'urutan',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
