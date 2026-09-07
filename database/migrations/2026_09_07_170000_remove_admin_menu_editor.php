<?php

use App\Models\Menu;
use Database\Seeders\AmiMenuSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The admin-sidebar menu editor (/admin/menu) has been removed; the admin
     * sidebar is now managed purely through the seeders. Only the website menu
     * editor (/admin/menu-web) remains.
     */
    public function up(): void
    {
        Menu::where('lokasi', 'admin')->where('route', 'admin.menu.index')->delete();

        // Re-normalise the top-level order without the removed "Menu" item.
        AmiMenuSeeder::rebuild();
    }

    public function down(): void
    {
        // Menu data only - reversal is done by re-running the seeders.
    }
};
