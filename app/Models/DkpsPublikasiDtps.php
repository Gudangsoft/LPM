<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPublikasiDtps extends Model
{
    protected $table = 'dkps_publikasi_dtps';

    protected $fillable = [
        'dkps_submission_id',
        'media_publikasi',
        'jumlah_ts2',
        'jumlah_ts1',
        'jumlah_ts',
        'urutan',
    ];

    const MEDIA_PUBLIKASI = [
        'jurnal_nasional_tidak_terakreditasi',
        'jurnal_nasional_terakreditasi',
        'jurnal_internasional_karya_monumental_nasional',
        'jurnal_internasional_bereputasi_karya_monumental_internasional',
        'seminar_wilayah_lokal_pt',
        'seminar_nasional',
        'seminar_internasional',
        'media_massa_wilayah',
        'media_massa_nasional',
        'media_massa_internasional',
        'buku_isbn_book_chapter',
        'paten_paten_sederhana',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
