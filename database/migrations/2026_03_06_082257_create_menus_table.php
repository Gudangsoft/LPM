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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Display name
            $table->string('icon')->nullable(); // Bootstrap icon class
            $table->string('route')->nullable(); // Named route
            $table->string('url')->nullable(); // Custom URL (if no route)
            $table->string('route_pattern')->nullable(); // For active state check
            $table->string('tipe')->default('link'); // link, section, divider
            $table->unsignedBigInteger('parent_id')->nullable(); // For submenu
            $table->string('badge_model')->nullable(); // Model class for badge count
            $table->string('badge_method')->nullable(); // Method to call for count
            $table->string('badge_class')->default('bg-danger'); // Badge CSS class
            $table->string('permission')->nullable(); // Permission required (future)
            $table->integer('urutan')->default(0); // Sort order
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
