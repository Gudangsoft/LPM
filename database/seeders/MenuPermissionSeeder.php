<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuPermissionSeeder extends Seeder
{
    /**
     * Patches `permission` onto menu rows created before RBAC was wired in,
     * and adds the "Penugasan Saya" entry if it doesn't exist yet.
     * Safe to re-run (idempotent).
     */
    public function run(): void
    {
        $map = [
            'admin.dashboard' => 'dashboard.view',
            'admin.dokumen.index' => 'dokumen.view',
            'admin.prodi.index' => 'prodi.view',
            'admin.akreditasi.index' => 'akreditasi.view',
            'admin.akreditasi.dashboard' => 'akreditasi.view',
            'admin.ami.periode.index' => 'periode-ami.view',
            'admin.ami.auditor.index' => 'auditor.view',
            'admin.ami.jadwal.index' => 'jadwal-ami.view',
            'admin.ami.temuan.index' => 'temuan.view',
            'admin.ami.tindak-lanjut.index' => 'tindak-lanjut.view',
        ];

        foreach ($map as $route => $permission) {
            Menu::where('route', $route)->update(['permission' => $permission]);
        }

        // Fix the pre-existing badge bug: TindakLanjut has no pendingCount method,
        // only pendingReview (a local scope, callable statically without 'scope').
        Menu::where('route', 'admin.ami.tindak-lanjut.index')
            ->where('badge_method', 'pendingCount')
            ->update(['badge_method' => 'pendingReview']);

        if (!Menu::where('route', 'admin.ami.penugasan.saya')->exists()) {
            $jadwalMenu = Menu::where('route', 'admin.ami.jadwal.index')->first();

            Menu::create([
                'nama' => 'Penugasan Saya',
                'tipe' => 'link',
                'route' => 'admin.ami.penugasan.saya',
                'route_pattern' => 'admin.ami.penugasan.*',
                'icon' => 'bi-inbox',
                'urutan' => ($jadwalMenu->urutan ?? Menu::max('urutan') ?? 0) + 1,
                'is_active' => true,
                'permission' => 'penugasan.respond',
            ]);
        }

        if (!Menu::where('route', 'admin.ami.standar-mutu.index')->exists()) {
            $tindakLanjutMenu = Menu::where('route', 'admin.ami.tindak-lanjut.index')->first();

            Menu::create([
                'nama' => 'Standar Mutu',
                'tipe' => 'link',
                'route' => 'admin.ami.standar-mutu.index',
                'route_pattern' => 'admin.ami.standar-mutu.*',
                'icon' => 'bi-list-check',
                'urutan' => ($tindakLanjutMenu->urutan ?? Menu::max('urutan') ?? 0) + 1,
                'is_active' => true,
                'permission' => 'standar-mutu.view',
            ]);
        }

        if (!Menu::where('route', 'admin.laporan.index')->exists()) {
            $standarMutuMenu = Menu::where('route', 'admin.ami.standar-mutu.index')->first();

            Menu::create([
                'nama' => 'Laporan',
                'tipe' => 'link',
                'route' => 'admin.laporan.index',
                'route_pattern' => 'admin.laporan.*',
                'icon' => 'bi-file-earmark-bar-graph',
                'urutan' => ($standarMutuMenu->urutan ?? Menu::max('urutan') ?? 0) + 1,
                'is_active' => true,
                'permission' => 'laporan-ami.view',
            ]);
        }

        if (!Menu::where('route', 'admin.panduan.index')->exists()) {
            $laporanMenu = Menu::where('route', 'admin.laporan.index')->first();

            Menu::create([
                'nama' => 'Buku Panduan',
                'tipe' => 'link',
                'route' => 'admin.panduan.index',
                'route_pattern' => 'admin.panduan.*',
                'icon' => 'bi-journal-bookmark',
                'urutan' => ($laporanMenu->urutan ?? Menu::max('urutan') ?? 0) + 1,
                'is_active' => true,
                'permission' => 'panduan.view',
            ]);
        }

        if (!Menu::where('route', 'admin.dkps.index')->exists()) {
            $panduanMenu = Menu::where('route', 'admin.panduan.index')->first();

            Menu::create([
                'nama' => 'DKPS',
                'tipe' => 'link',
                'route' => 'admin.dkps.index',
                'route_pattern' => 'admin.dkps.*',
                'icon' => 'bi-clipboard-data',
                'urutan' => ($panduanMenu->urutan ?? Menu::max('urutan') ?? 0) + 1,
                'is_active' => true,
                'permission' => 'dkps.view',
            ]);
        }

        // Move the generic "Sistem" section (Pesan Kontak/Pengguna/Menu/Pengaturan/
        // Database) to sort after the Prodi/Akreditasi/AMI sections instead of
        // between them - those are the day-to-day sections for most roles.
        $sistemOrder = [
            'Sistem' => 100,
            'Pesan Kontak' => 101,
            'Pengguna' => 102,
            'Menu' => 103,
            'Pengaturan' => 104,
            'Database' => 105,
        ];

        foreach ($sistemOrder as $nama => $urutan) {
            Menu::where('nama', $nama)->whereNull('parent_id')->update(['urutan' => $urutan]);
        }

        // Query-builder update() calls above bypass Eloquent's 'saved' event, so
        // Menu's boot() cache-clear hook never fires for them - clear explicitly.
        Menu::clearCache();
    }
}
