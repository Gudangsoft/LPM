<?php

namespace App\Models;

use App\Models\Concerns\RecordsAuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rtm extends Model
{
    use RecordsAuditTrail;

    protected $table = 'rtm';

    protected $fillable = [
        'periode_ami_id',
        'judul',
        'tanggal',
        'tempat',
        'pemimpin',
        'notulen',
        'status',
        'ringkasan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function periodeAmi(): BelongsTo
    {
        return $this->belongsTo(PeriodeAmi::class);
    }

    public function agenda(): HasMany
    {
        return $this->hasMany(RtmAgenda::class)->orderBy('urutan')->orderBy('id');
    }

    public function keputusan(): HasMany
    {
        return $this->hasMany(RtmKeputusan::class)->orderBy('urutan')->orderBy('id');
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'selesai' ? 'success' : 'secondary';
    }
}
