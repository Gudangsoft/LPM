<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Mata kuliah per prodi, ditarik dari SIAKAD. */
class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = ['prodi_id', 'kode_mk', 'nama', 'semester', 'sks'];

    protected $casts = [
        'semester' => 'integer',
        'sks' => 'float',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}
