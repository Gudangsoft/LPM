<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DosenTetap extends Model
{
    protected $table = 'dosen_tetap';

    protected $fillable = [
        'dkps_submission_id',
        'nama',
        'nidn_nidk',
        'nuptk',
        'pendidikan_magister_bidang',
        'pendidikan_doktor_bidang',
        'bidang_keahlian',
        'jabatan_akademik',
        'no_sertifikat_pendidik',
        'mk_diampu_ps_diakreditasi',
        'mk_diampu_ps_lain',
        'urutan',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }

    public function bebanKerja(): HasMany
    {
        return $this->hasMany(DkpsBebanKerjaDtps::class, 'dosen_tetap_id');
    }

    public function rekognisi(): HasMany
    {
        return $this->hasMany(DkpsRekognisiDtps::class, 'dosen_tetap_id');
    }
}
