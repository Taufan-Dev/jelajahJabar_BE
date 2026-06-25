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
        Schema::table('wisatas', function (Blueprint $table) {
            $table->index('status', 'wisatas_status_index');
            $table->index('kategori', 'wisatas_kategori_index');
        });

        Schema::table('tikets', function (Blueprint $table) {
            $table->index('status_pembayaran', 'tikets_status_pembayaran_index');
            $table->index('status_tiket', 'tikets_status_tiket_index');
            $table->index('tanggal_kunjungan', 'tikets_tanggal_kunjungan_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wisatas', function (Blueprint $table) {
            $table->dropIndex('wisatas_status_index');
            $table->dropIndex('wisatas_kategori_index');
        });

        Schema::table('tikets', function (Blueprint $table) {
            $table->dropIndex('tikets_status_pembayaran_index');
            $table->dropIndex('tikets_status_tiket_index');
            $table->dropIndex('tikets_tanggal_kunjungan_index');
        });
    }
};
