<?php

use App\Models\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('lokasi', 20)->default('admin')->after('tipe')->index();
        });

        // Existing rows are all the admin sidebar.
        DB::table('menus')->update(['lokasi' => 'admin']);

        $this->addAdminSidebarLink();
        $this->seedFrontendMenu();
    }

    public function down(): void
    {
        DB::table('menus')->where('lokasi', 'frontend')->delete();
        DB::table('menus')->where('route', 'admin.menu-web.index')->delete();

        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex(['lokasi']);
            $table->dropColumn('lokasi');
        });
    }

    /**
     * Add a "Menu Website" entry to the admin sidebar next to "Manajemen Menu".
     */
    private function addAdminSidebarLink(): void
    {
        if (DB::table('menus')->where('route', 'admin.menu-web.index')->exists()) {
            return;
        }

        $anchor = DB::table('menus')->where('route', 'admin.menu.index')->first();

        DB::table('menus')->insert([
            'nama' => 'Menu Website',
            'icon' => 'bi-list-nested',
            'route' => 'admin.menu-web.index',
            'route_pattern' => 'admin.menu-web.*',
            'tipe' => 'link',
            'lokasi' => 'admin',
            'parent_id' => null,
            'urutan' => $anchor ? $anchor->urutan : 900,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Seed the public navbar so nothing changes visually after deploy.
     * Idempotent: skipped when frontend rows already exist.
     */
    private function seedFrontendMenu(): void
    {
        if (DB::table('menus')->where('lokasi', 'frontend')->exists()) {
            return;
        }

        $order = 0;
        $make = function (array $attr) use (&$order): int {
            return DB::table('menus')->insertGetId(array_merge([
                'icon' => null,
                'route' => null,
                'url' => null,
                'route_pattern' => null,
                'tipe' => 'link',
                'lokasi' => 'frontend',
                'parent_id' => null,
                'badge_class' => 'bg-danger',
                'permission' => null,
                'urutan' => ++$order,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $attr));
        };

        $doc = fn ($kat) => '/dokumen?kategori=' . rawurlencode($kat);

        $make(['nama' => 'Beranda', 'route' => 'home', 'route_pattern' => 'home']);

        $profil = $make(['nama' => 'Profil']);
        foreach ([
            ['Profil LPM', 'profil'],
            ['Visi & Misi', 'visi-misi'],
            ['Struktur Organisasi', 'struktur-organisasi'],
        ] as [$n, $r]) {
            $make(['nama' => $n, 'route' => $r, 'parent_id' => $profil]);
        }

        $spmi = $make(['nama' => 'SPMI']);
        $make(['nama' => 'Sistem Penjaminan Mutu', 'route' => 'sistem-penjaminan-mutu', 'parent_id' => $spmi]);
        foreach (['Kebijakan Mutu' => 'Kebijakan', 'Manual Mutu' => 'Manual', 'Standar Mutu' => 'Standar', 'Formulir Mutu' => 'Formulir', 'SOP' => 'SOP'] as $n => $kat) {
            $make(['nama' => $n, 'url' => $doc($kat), 'parent_id' => $spmi]);
        }

        $ami = $make(['nama' => 'AMI']);
        $make(['nama' => 'Audit Mutu Internal', 'route' => 'audit-mutu-internal', 'parent_id' => $ami]);
        $make(['nama' => 'Laporan AMI', 'url' => $doc('Laporan'), 'parent_id' => $ami]);
        $make(['nama' => 'Panduan AMI', 'url' => $doc('Panduan'), 'parent_id' => $ami]);

        $akr = $make(['nama' => 'Akreditasi']);
        $make(['nama' => 'Data Akreditasi', 'route' => 'akreditasi', 'parent_id' => $akr]);
        $make(['nama' => 'Dokumen Akreditasi', 'url' => $doc('Akreditasi'), 'parent_id' => $akr]);
        $make(['nama' => 'Surat Keputusan (SK)', 'url' => $doc('SK'), 'parent_id' => $akr]);

        $dok = $make(['nama' => 'Dokumen', 'route' => 'dokumen.index', 'route_pattern' => 'dokumen.*']);
        $make(['nama' => 'Semua Dokumen', 'route' => 'dokumen.index', 'parent_id' => $dok]);

        $info = $make(['nama' => 'Informasi']);
        foreach ([
            ['Berita', 'berita.index'],
            ['Pengumuman', 'pengumuman.index'],
            ['Agenda', 'agenda.index'],
            ['Galeri', 'galeri.index'],
        ] as [$n, $r]) {
            $make(['nama' => $n, 'route' => $r, 'parent_id' => $info]);
        }

        $tautan = $make(['nama' => 'Tautan']);
        foreach ([
            ['BAN-PT', 'https://www.banpt.or.id'],
            ['LAMEMBA', 'https://lamemba.or.id'],
            ['PDDikti', 'https://pddikti.kemdikbud.go.id'],
            ['SINTA', 'https://sinta.kemdikbud.go.id'],
            ['SPMI Dikti', 'https://spmi.kemdikbud.go.id'],
        ] as [$n, $u]) {
            $make(['nama' => $n, 'url' => $u, 'parent_id' => $tautan]);
        }

        $make(['nama' => 'Kontak', 'route' => 'kontak.index', 'route_pattern' => 'kontak.*']);

        Menu::clearCache();
    }
};
