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
            $table->boolean('buka_tab')->default(false)->after('url');
        });

        // Seeded external links (BAN-PT, SINTA, …) were stored with a full http(s) URL.
        DB::table('menus')->where('url', 'like', 'http%')->update(['buka_tab' => true]);

        Menu::clearCache();
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('buka_tab');
        });
    }
};
