<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenVersion extends Model
{
    public $timestamps = false;

    protected $table = 'dokumen_versions';

    protected $fillable = [
        'dokumen_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'version_number',
        'uploaded_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
