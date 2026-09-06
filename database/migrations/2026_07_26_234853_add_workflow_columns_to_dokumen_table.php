<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->after('is_active');
            $table->foreignId('uploaded_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->after('uploaded_by')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('catatan_reviewer')->nullable()->after('reviewed_at');
            $table->unsignedInteger('current_version')->default(1)->after('catatan_reviewer');
        });

        // Backfill: documents that already existed before this workflow was
        // introduced stay publicly visible - only new uploads start at draft.
        DB::table('dokumen')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['status', 'reviewed_at', 'catatan_reviewer', 'current_version']);
        });
    }
};
