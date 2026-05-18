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
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('wisata_id')->constrained('wisatas')->cascadeOnDelete();
            $table->integer('jumlah_tiket');
            $table->integer('total_harga');
            $table->enum('status_pembayaran', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('status_tiket', ['unused', 'used'])->default('unused');
            $table->date('tanggal_kunjungan');
            $table->dateTime('tanggal_digunakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tikets');
    }
};
