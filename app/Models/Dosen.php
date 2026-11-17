<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dosen extends Model
{
    protected $table = 'dosen';

    public const JABATAN = [
        'tenaga_pengajar' => 'Tenaga Pengajar',
        'asisten_ahli' => 'Asisten Ahli',
        'lektor' => 'Lektor',
        'lektor_kepala' => 'Lektor Kepala',
        'guru_besar' => 'Guru Besar',
    ];

    public const JENIS_KELAMIN = ['L' => 'Laki-laki', 'P' => 'Perempuan'];

    public const PENDIDIKAN = ['S1', 'S2', 'S3', 'Sp-1', 'Sp-2', 'Profesi'];

    protected $fillable = [
        'prodi_id',
        'kode_dosen',
        'nama',
        'jenis_kelamin',
        'nidn_nidk',
        'nuptk',
        'email',
        'no_hp',
        'alamat',
        'pendidikan_terakhir',
        'lulusan',
        'pendidikan_magister_bidang',
        'pendidikan_doktor_bidang',
        'bidang_keahlian',
        'golongan',
        'jabatan_akademik',
        'no_sertifikat_pendidik',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /** Hanya dosen pada prodi yang dikepalai user (kaprodi). */
    public function scopeOwnedByKaprodi($query, User $user)
    {
        return $query->whereHas('prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
    }

    /** Dosen yang boleh dilihat/dikelola user: semua untuk admin, prodi sendiri untuk kaprodi. */
    public function scopeVisibleTo($query, User $user)
    {
        return $query->when(! $user->isAdmin() && $user->isKaprodi(), fn ($q) => $q->ownedByKaprodi($user));
    }

    public function getJabatanLabelAttribute(): string
    {
        return self::JABATAN[$this->jabatan_akademik] ?? '-';
    }
}
