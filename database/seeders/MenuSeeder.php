<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset only the admin sidebar menu; the public website menu (lokasi=frontend) is kept.
        Menu::where('lokasi', 'admin')->delete();

        $menus = [
            // Dashboard
            [
                'nama' => 'Dashboard',
                'icon' => 'bi-speedometer2',
                'route' => 'admin.dashboard',
                'route_pattern' => 'admin.dashboard',
                'tipe' => 'link',
                'urutan' => 1,
                'permission' => 'dashboard.view',
            ],

            // Content Section
            [
                'nama' => 'Konten',
                'tipe' => 'section',
                'urutan' => 2,
            ],
            [
                'nama' => 'Berita',
                'icon' => 'bi-newspaper',
                'route' => 'admin.berita.index',
                'route_pattern' => 'admin.berita.*',
                'tipe' => 'link',
                'urutan' => 3,
            ],
            [
                'nama' => 'Kategori',
                'icon' => 'bi-folder',
                'route' => 'admin.kategori-berita.index',
                'route_pattern' => 'admin.kategori-berita.*',
                'tipe' => 'link',
                'urutan' => 4,
            ],
            [
                'nama' => 'Pengumuman',
                'icon' => 'bi-megaphone',
                'route' => 'admin.pengumuman.index',
                'route_pattern' => 'admin.pengumuman.*',
                'tipe' => 'link',
                'urutan' => 5,
            ],
            [
                'nama' => 'Agenda',
                'icon' => 'bi-calendar-event',
                'route' => 'admin.agenda.index',
                'route_pattern' => 'admin.agenda.*',
                'tipe' => 'link',
                'urutan' => 6,
            ],

            // Media Section
            [
                'nama' => 'Media',
                'tipe' => 'section',
                'urutan' => 7,
            ],
            [
                'nama' => 'Galeri',
                'icon' => 'bi-images',
                'route' => 'admin.galeri.index',
                'route_pattern' => 'admin.galeri.*',
                'tipe' => 'link',
                'urutan' => 8,
            ],
            [
                'nama' => 'Dokumen',
                'icon' => 'bi-file-earmark-pdf',
                'route' => 'admin.dokumen.index',
                'route_pattern' => 'admin.dokumen.*',
                'tipe' => 'link',
                'urutan' => 9,
            ],
            [
                'nama' => 'Slider',
                'icon' => 'bi-card-image',
                'route' => 'admin.sliders.index',
                'route_pattern' => 'admin.sliders.*',
                'tipe' => 'link',
                'urutan' => 10,
            ],

            // Pages Section
            [
                'nama' => 'Halaman',
                'tipe' => 'section',
                'urutan' => 11,
            ],
            [
                'nama' => 'Halaman',
                'icon' => 'bi-file-text',
                'route' => 'admin.halaman.index',
                'route_pattern' => 'admin.halaman.*',
                'tipe' => 'link',
                'urutan' => 12,
            ],
            [
                'nama' => 'Struktur Organisasi',
                'icon' => 'bi-diagram-3',
                'route' => 'admin.struktur-organisasi.index',
                'route_pattern' => 'admin.struktur-organisasi.*',
                'tipe' => 'link',
                'urutan' => 13,
            ],

            // System Section
            [
                'nama' => 'Sistem',
                'tipe' => 'section',
                'urutan' => 14,
            ],
            [
                'nama' => 'Pesan Kontak',
                'icon' => 'bi-envelope',
                'route' => 'admin.kontak.index',
                'route_pattern' => 'admin.kontak.*',
                'tipe' => 'link',
                'urutan' => 15,
                'badge_model' => '\App\Models\Kontak',
                'badge_method' => 'unread',
                'badge_class' => 'bg-danger',
            ],
            [
                'nama' => 'Pengguna',
                'icon' => 'bi-people',
                'route' => 'admin.users.index',
                'route_pattern' => 'admin.users.*',
                'tipe' => 'link',
                'urutan' => 16,
            ],
            [
                'nama' => 'Menu',
                'icon' => 'bi-list-nested',
                'route' => 'admin.menu.index',
                'route_pattern' => 'admin.menu.*',
                'tipe' => 'link',
                'urutan' => 17,
            ],
            [
                'nama' => 'Pengaturan',
                'icon' => 'bi-gear',
                'route' => 'admin.pengaturan.index',
                'route_pattern' => 'admin.pengaturan.*',
                'tipe' => 'link',
                'urutan' => 18,
            ],
            [
                'nama' => 'Database',
                'icon' => 'bi-database',
                'route' => 'admin.database.index',
                'route_pattern' => 'admin.database.*',
                'tipe' => 'link',
                'urutan' => 19,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu + ['lokasi' => 'admin']);
        }

        // Keep the "Menu Website" sidebar entry that the migration adds.
        if (! Menu::where('route', 'admin.menu-web.index')->exists()) {
            Menu::create([
                'nama' => 'Menu Website',
                'icon' => 'bi-list-nested',
                'route' => 'admin.menu-web.index',
                'route_pattern' => 'admin.menu-web.*',
                'tipe' => 'link',
                'lokasi' => 'admin',
                'urutan' => 17,
            ]);
        }

        Menu::clearCache();
    }
}
