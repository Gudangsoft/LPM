<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPengembanganKompetensi extends Model
{
    protected $table = 'dkps_pengembangan_kompetensi';

    protected $fillable = [
        'dkps_submission_id',
        'person_type',
        'dosen_tetap_id',
        'tenaga_kependidikan_id',
        'deskripsi_kegiatan',
        'tempat',
        'waktu_pelaksanaan',
        'manfaat',
        'bukti_file',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }

    public function dosenTetap(): BelongsTo
    {
        return $this->belongsTo(DosenTetap::class, 'dosen_tetap_id');
    }

    public function tenagaKependidikan(): BelongsTo
    {
        return $this->belongsTo(TenagaKependidikan::class, 'tenaga_kependidikan_id');
    }

    public function scopePersonType($query, string $type)
    {
        return $query->where('person_type', $type);
    }
}
