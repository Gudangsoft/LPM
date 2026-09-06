<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DkpsSubmission extends Model
{
    protected $table = 'dkps_submissions';

    protected $fillable = [
        'prodi_id',
        'akreditasi_id',
        'tahun_ts_awal',
        'tahun_ts_akhir',
        'nama_pengusul',
        'tanggal_pengusulan',
        'status',
    ];

    protected $casts = [
        'tanggal_pengusulan' => 'date',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function akreditasi(): BelongsTo
    {
        return $this->belongsTo(Akreditasi::class);
    }

    /**
     * Scope to only records belonging to a prodi the given user heads (kaprodi_id).
     */
    public function scopeOwnedByKaprodi($query, User $user)
    {
        return $query->whereHas('prodi', fn ($q) => $q->where('kaprodi_id', $user->id));
    }

    public function dosenTetap(): HasMany
    {
        return $this->hasMany(DosenTetap::class)->orderBy('urutan');
    }

    public function tenagaKependidikan(): HasMany
    {
        return $this->hasMany(TenagaKependidikan::class)->orderBy('urutan');
    }

    public function kerjasama(): HasMany
    {
        return $this->hasMany(DkpsKerjasama::class)->orderBy('urutan');
    }

    public function kualitasInput(): HasMany
    {
        return $this->hasMany(DkpsKualitasInput::class);
    }

    public function prestasiMahasiswa(): HasMany
    {
        return $this->hasMany(DkpsPrestasiMahasiswa::class)->orderBy('urutan');
    }

    public function karyaInovatifMahasiswa(): HasMany
    {
        return $this->hasMany(DkpsKaryaInovatifMahasiswa::class)->orderBy('urutan');
    }

    public function kepuasanMahasiswa(): HasMany
    {
        return $this->hasMany(DkpsKepuasanMahasiswa::class);
    }

    public function bebanKerjaDtps(): HasMany
    {
        return $this->hasMany(DkpsBebanKerjaDtps::class);
    }

    public function rekognisiDtps(): HasMany
    {
        return $this->hasMany(DkpsRekognisiDtps::class);
    }

    public function pengembanganKompetensi(): HasMany
    {
        return $this->hasMany(DkpsPengembanganKompetensi::class);
    }

    public function tenagaKependidikanSummary(): HasMany
    {
        return $this->hasMany(DkpsTenagaKependidikanSummary::class);
    }

    public function penggunaanDana(): HasMany
    {
        return $this->hasMany(DkpsPenggunaanDana::class)->orderBy('urutan');
    }

    public function saranaLab(): HasMany
    {
        return $this->hasMany(DkpsSaranaLab::class)->orderBy('urutan');
    }

    public function prasarana(): HasMany
    {
        return $this->hasMany(DkpsPrasarana::class)->orderBy('urutan');
    }

    public function tik(): HasMany
    {
        return $this->hasMany(DkpsTik::class)->orderBy('urutan');
    }

    public function kurikulum(): HasMany
    {
        return $this->hasMany(DkpsKurikulum::class)->orderBy('urutan');
    }

    public function integrasiPenelitianPkm(): HasMany
    {
        return $this->hasMany(DkpsIntegrasiPenelitianPkm::class)->orderBy('urutan');
    }

    public function pembimbinganMagang(): HasMany
    {
        return $this->hasMany(DkpsPembimbinganMagang::class);
    }

    public function kegiatanLuarKelas(): HasMany
    {
        return $this->hasMany(DkpsKegiatanLuarKelas::class)->orderBy('urutan');
    }

    public function pembimbinganTa(): HasMany
    {
        return $this->hasMany(DkpsPembimbinganTa::class);
    }

    public function ipkLulusan(): HasMany
    {
        return $this->hasMany(DkpsIpkLulusan::class);
    }

    public function masaStudiLulusan(): HasMany
    {
        return $this->hasMany(DkpsMasaStudiLulusan::class);
    }

    public function lulusanBekerja(): HasMany
    {
        return $this->hasMany(DkpsLulusanBekerja::class);
    }

    public function waktuTunggu(): HasMany
    {
        return $this->hasMany(DkpsWaktuTunggu::class);
    }

    public function kesesuaianBidang(): HasMany
    {
        return $this->hasMany(DkpsKesesuaianBidang::class);
    }

    public function kepuasanPenggunaReferensi(): HasMany
    {
        return $this->hasMany(DkpsKepuasanPenggunaReferensi::class);
    }

    public function kepuasanPenggunaKemampuan(): HasMany
    {
        return $this->hasMany(DkpsKepuasanPenggunaKemampuan::class)->orderBy('urutan');
    }

    public function penelitianPkmRingkasan(): HasMany
    {
        return $this->hasMany(DkpsPenelitianPkmRingkasan::class);
    }

    public function penelitianPkmMahasiswa(): HasMany
    {
        return $this->hasMany(DkpsPenelitianPkmMahasiswa::class)->orderBy('urutan');
    }

    public function publikasiDtps(): HasMany
    {
        return $this->hasMany(DkpsPublikasiDtps::class)->orderBy('urutan');
    }

    public function publikasiDtpsDetail(): HasMany
    {
        return $this->hasMany(DkpsPublikasiDtpsDetail::class)->orderBy('urutan');
    }

    public function sitasiDtps(): HasMany
    {
        return $this->hasMany(DkpsSitasiDtps::class)->orderBy('urutan');
    }

    public function getTahunAjaranAttribute(): string
    {
        return "{$this->tahun_ts_awal}/{$this->tahun_ts_akhir}";
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'final' => 'success',
            default => 'secondary',
        };
    }
}
