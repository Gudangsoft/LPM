<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_diri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ami_id')->constrained('jadwal_ami')->cascadeOnDelete();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('jadwal_ami_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_diri');
    }
};
