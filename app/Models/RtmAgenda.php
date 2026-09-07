<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RtmAgenda extends Model
{
    protected $table = 'rtm_agenda';

    protected $fillable = ['rtm_id', 'topik', 'pembahasan', 'urutan'];

    public function rtm(): BelongsTo
    {
        return $this->belongsTo(Rtm::class);
    }
}
