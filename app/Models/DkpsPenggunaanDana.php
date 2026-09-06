<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DkpsPenggunaanDana extends Model
{
    protected $table = 'dkps_penggunaan_dana';

    protected $fillable = [
        'dkps_submission_id',
        'kategori',
        'sub_item',
        'jenis_penggunaan',
        'up_ps_ts2',
        'up_ps_ts1',
        'up_ps_ts',
        'ps_ts2',
        'ps_ts1',
        'ps_ts',
        'urutan',
    ];

    const KATEGORI = [
        'biaya_operasional_pendidikan' => 'Biaya Operasional Pendidikan',
        'operasional_penelitian' => 'Operasional Penelitian',
        'operasional_pkm' => 'Operasional PkM',
        'investasi_sdm' => 'Investasi SDM',
        'investasi_sarana' => 'Investasi Sarana',
        'investasi_prasarana' => 'Investasi Prasarana',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DkpsSubmission::class, 'dkps_submission_id');
    }
}
