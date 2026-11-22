<?php

use App\Models\Menu;
use Illuminate\Database\Migrations\Migration;

/**
 * Menu "Tarik Data SIAKAD" (khusus admin: permission kosong) di bawah "Data Dosen".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Menu::where('route', 'admin.siakad.index')->exists()) {
            return;
        }

        $after = Menu::where('lokasi', 'admin')->where('route', 'admin.dosen.index')->first()
            ?? Menu::where('lokasi', 'admin')->where('route', 'admin.prodi.index')->first();

        Menu::create([
            'nama' => 'Tarik Data SIAKAD',
            'route' => 'admin.siakad.index',
            'route_pattern' => 'admin.siakad.*',
            'icon' => 'bi-cloud-download',
            'permission' => null,
            'tipe' => 'link',
            'lokasi' => 'admin',
            'parent_id' => $after?->parent_id,
            'urutan' => ($after?->urutan ?? 0) + 1,
            'is_active' => true,
        ]);

        Menu::clearCache();
    }

    public function down(): void
    {
        Menu::where('route', 'admin.siakad.index')->delete();
        Menu::clearCache();
    }
};
