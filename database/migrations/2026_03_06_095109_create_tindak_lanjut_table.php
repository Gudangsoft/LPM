<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temuan_ami_id')->constrained('temuan_ami')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // PIC
            $table->text('deskripsi');
            $table->string('file_bukti')->nullable();
            $table->date('tanggal_submit');
            $table->enum('status', ['submitted', 'reviewed', 'approved', 'rejected'])->default('submitted');
            $table->text('catatan_reviewer')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
