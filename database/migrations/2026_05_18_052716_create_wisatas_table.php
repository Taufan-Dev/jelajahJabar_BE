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
        Schema::create('wisatas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wisata');
            $table->text('deskripsi');
            $table->text('lokasi');
            $table->decimal('harga_tiket', 12, 2);
            $table->foreignId('id_pengelola')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_wilayah')->constrained('wilayahs')->cascadeOnDelete();
            $table->enum('status', ['pending', 'disetujui_admin', 'disetujui_super_admin'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisatas');
    }
};
