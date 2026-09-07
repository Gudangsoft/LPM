<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ButirInstrumen extends Model
{
    use HasFactory;
    use RecordsAuditTrail;

    protected $table = 'butir_instrumen';

    protected $fillable = [
        'standar_mutu_id',
        'kode',
        'pertanyaan',
        'indikator',
        'bobot',
        'target',
        'jenis_bukti',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function standarMutu(): BelongsTo
    {
        return $this->belongsTo(StandarMutu::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('id');
    }
}
