<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenagaKependidikan extends Model
{
    protected $table = 'tenaga_kependidikan';

    protected $fillable = [
        'dkps_submission_id',
        'nama',
        'jenis',
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
}
