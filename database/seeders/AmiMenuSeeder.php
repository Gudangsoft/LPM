<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class AmiMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the last order number
        $lastOrder = Menu::max('urutan') ?? 0;

        // AMI Section Header
        $amiSection = Menu::create([
            'nama' => 'PENJAMINAN MUTU',
            'tipe' => 'section',
            'urutan' => $lastOrder + 1,
            'is_active' => true,
        ]);

        // Program Studi
        Menu::create([
            'nama' => 'Program Studi',
            'tipe' => 'link',
            'route' => 'admin.prodi.index',
            'route_pattern' => 'admin.prodi.*',
            'icon' => 'bi-mortarboard',
            'urutan' => $lastOrder + 2,
            'is_active' => true,
            'permission' => 'prodi.view',
        ]);

        // Akreditasi
        Menu::create([
            'nama' => 'Akreditasi',
            'tipe' => 'link',
            'route' => 'admin.akreditasi.index',
            'route_pattern' => 'admin.akreditasi.*',
            'icon' => 'bi-award',
            'urutan' => $lastOrder + 3,
            'is_active' => true,
            'permission' => 'akreditasi.view',
        ]);

        // Dashboard Akreditasi
        Menu::create([
            'nama' => 'Dashboard Akreditasi',
            'tipe' => 'link',
            'route' => 'admin.akreditasi.dashboard',
            'icon' => 'bi-speedometer2',
            'urutan' => $lastOrder + 4,
            'is_active' => true,
            'permission' => 'akreditasi.view',
        ]);

        // AMI Section
        $amiMenu = Menu::create([
            'nama' => 'AUDIT MUTU INTERNAL',
            'tipe' => 'section',
            'urutan' => $lastOrder + 5,
            'is_active' => true,
        ]);

        // Periode AMI
        Menu::create([
            'nama' => 'Periode AMI',
            'tipe' => 'link',
            'route' => 'admin.ami.periode.index',
            'route_pattern' => 'admin.ami.periode.*',
            'icon' => 'bi-calendar3',
            'urutan' => $lastOrder + 6,
            'is_active' => true,
            'permission' => 'periode-ami.view',
        ]);

        // Auditor
        Menu::create([
            'nama' => 'Auditor',
            'tipe' => 'link',
            'route' => 'admin.ami.auditor.index',
            'route_pattern' => 'admin.ami.auditor.*',
            'icon' => 'bi-person-badge',
            'urutan' => $lastOrder + 7,
            'is_active' => true,
            'permission' => 'auditor.view',
        ]);

        // Jadwal AMI
        Menu::create([
            'nama' => 'Jadwal Audit',
            'tipe' => 'link',
            'route' => 'admin.ami.jadwal.index',
            'route_pattern' => 'admin.ami.jadwal.*',
            'icon' => 'bi-calendar-check',
            'urutan' => $lastOrder + 8,
            'is_active' => true,
            'permission' => 'jadwal-ami.view',
        ]);

        // Penugasan Saya (auditor's own assignment inbox)
        Menu::create([
            'nama' => 'Penugasan Saya',
            'tipe' => 'link',
            'route' => 'admin.ami.penugasan.saya',
            'route_pattern' => 'admin.ami.penugasan.*',
            'icon' => 'bi-inbox',
            'urutan' => $lastOrder + 9,
            'is_active' => true,
            'permission' => 'penugasan.respond',
        ]);

        // Temuan AMI
        Menu::create([
            'nama' => 'Temuan',
            'tipe' => 'link',
            'route' => 'admin.ami.temuan.index',
            'route_pattern' => 'admin.ami.temuan.*',
            'icon' => 'bi-search',
            'urutan' => $lastOrder + 10,
            'is_active' => true,
            'permission' => 'temuan.view',
        ]);

        // Tindak Lanjut
        Menu::create([
            'nama' => 'Tindak Lanjut',
            'tipe' => 'link',
            'route' => 'admin.ami.tindak-lanjut.index',
            'route_pattern' => 'admin.ami.tindak-lanjut.*',
            'icon' => 'bi-clipboard-check',
            'urutan' => $lastOrder + 11,
            'is_active' => true,
            'permission' => 'tindak-lanjut.view',
            'badge_model' => 'App\\Models\\TindakLanjut',
            'badge_method' => 'pendingReview',
            'badge_class' => 'bg-warning',
        ]);

        // Standar Mutu
        Menu::create([
            'nama' => 'Standar Mutu',
            'tipe' => 'link',
            'route' => 'admin.ami.standar-mutu.index',
            'route_pattern' => 'admin.ami.standar-mutu.*',
            'icon' => 'bi-list-check',
            'urutan' => $lastOrder + 12,
            'is_active' => true,
            'permission' => 'standar-mutu.view',
        ]);

        // Laporan
        Menu::create([
            'nama' => 'Laporan',
            'tipe' => 'link',
            'route' => 'admin.laporan.index',
            'route_pattern' => 'admin.laporan.*',
            'icon' => 'bi-file-earmark-bar-graph',
            'urutan' => $lastOrder + 13,
            'is_active' => true,
            'permission' => 'laporan-ami.view',
        ]);

        // Buku Panduan
        Menu::create([
            'nama' => 'Buku Panduan',
            'tipe' => 'link',
            'route' => 'admin.panduan.index',
            'route_pattern' => 'admin.panduan.*',
            'icon' => 'bi-journal-bookmark',
            'urutan' => $lastOrder + 14,
            'is_active' => true,
            'permission' => 'panduan.view',
        ]);

        // DKPS
        Menu::create([
            'nama' => 'DKPS',
            'tipe' => 'link',
            'route' => 'admin.dkps.index',
            'route_pattern' => 'admin.dkps.*',
            'icon' => 'bi-clipboard-data',
            'urutan' => $lastOrder + 15,
            'is_active' => true,
            'permission' => 'dkps.view',
        ]);
    }
}
