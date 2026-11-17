<?php

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Data induk Dosen per program studi. Dipakai ulang oleh DKPS Tabel 6
 * (tombol "Ambil dari Data Dosen") dan sebagai saran isian Dosen Pengampu
 * di Evaluasi Pembelajaran / Review RPS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained('prodi')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nidn_nidk', 50)->nullable()->unique();
            $table->string('nuptk', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('pendidikan_magister_bidang')->nullable();
            $table->string('pendidikan_doktor_bidang')->nullable();
            $table->string('bidang_keahlian')->nullable();
            $table->enum('jabatan_akademik', ['tenaga_pengajar', 'asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar'])->nullable();
            $table->string('no_sertifikat_pendidik', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['prodi_id', 'nama']);
        });

        $perms = [
            'dosen.view' => 'Lihat Data Dosen',
            'dosen.manage' => 'Kelola Data Dosen',
        ];
        foreach ($perms as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name, 'module' => 'ami']);
        }
        $ids = fn (array $slugs) => Permission::whereIn('slug', $slugs)->pluck('id');

        foreach (['admin', 'kaprodi'] as $slug) {
            Role::where('slug', $slug)->first()?->permissions()->syncWithoutDetaching($ids(['dosen.view', 'dosen.manage']));
        }
        foreach (['auditor', 'viewer'] as $slug) {
            Role::where('slug', $slug)->first()?->permissions()->syncWithoutDetaching($ids(['dosen.view']));
        }

        // Sisipkan menu langsung di bawah "Program Studi" (P-1 Penetapan) tanpa
        // membangun ulang seluruh sidebar.
        $prodiMenu = Menu::where('lokasi', 'admin')->where('route', 'admin.prodi.index')->first();
        if ($prodiMenu && ! Menu::where('route', 'admin.dosen.index')->exists()) {
            Menu::create([
                'nama' => 'Data Dosen',
                'route' => 'admin.dosen.index',
                'route_pattern' => 'admin.dosen.*',
                'icon' => 'bi-person-badge',
                'permission' => 'dosen.view',
                'tipe' => 'link',
                'lokasi' => 'admin',
                'parent_id' => $prodiMenu->parent_id,
                'urutan' => $prodiMenu->urutan + 1,
                'is_active' => true,
                'badge_class' => 'bg-danger',
            ]);
        }
    }

    public function down(): void
    {
        Menu::where('route', 'admin.dosen.index')->delete();
        Schema::dropIfExists('dosen');
    }
};
