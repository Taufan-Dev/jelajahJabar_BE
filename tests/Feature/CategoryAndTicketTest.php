<?php

use App\Models\User;
use App\Models\Wisata;
use App\Models\Tiket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengunjung dapat mengambil daftar kategori wisata', function () {
    $response = $this->getJson('/api/kategori');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Daftar kategori wisata berhasil diambil',
            'data' => ['Alam', 'Budaya', 'Rekreasi', 'Edukasi']
        ]);
});

test('pengunjung dapat memfilter wisata berdasarkan kategori', function () {
    // Buat wilayah
    $wilayah = \App\Models\Wilayah::create(['nama_kabupaten' => 'Bandung']);

    // Buat pengelola
    $pengelola = User::factory()->create(['role' => 'pengelola']);

    // Buat wisata Alam
    $wisataAlam = Wisata::create([
        'nama_wisata' => 'Kawah Putih',
        'deskripsi' => 'Deskripsi Kawah Putih',
        'lokasi' => 'Ciwidey',
        'harga_tiket' => 30000,
        'kategori' => 'Alam',
        'id_pengelola' => $pengelola->id,
        'id_wilayah' => $wilayah->id,
        'status' => 'disetujui_super_admin',
    ]);

    // Buat wisata Budaya
    $wisataBudaya = Wisata::create([
        'nama_wisata' => 'Saung Angklung',
        'deskripsi' => 'Deskripsi Saung Angklung',
        'lokasi' => 'Bandung',
        'harga_tiket' => 50000,
        'kategori' => 'Budaya',
        'id_pengelola' => $pengelola->id,
        'id_wilayah' => $wilayah->id,
        'status' => 'disetujui_super_admin',
    ]);

    // Filter kategori Alam
    $responseAlam = $this->getJson('/api/wisata?kategori=Alam');
    $responseAlam->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['nama_wisata' => 'Kawah Putih']);

    // Filter kategori Budaya
    $responseBudaya = $this->getJson('/api/wisata?kategori=Budaya');
    $responseBudaya->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['nama_wisata' => 'Saung Angklung']);
});

test('pengunjung dapat mengakses QR Code tiket dalam format SVG', function () {
    $user = User::factory()->create(['role' => 'user']);
    $wilayah = \App\Models\Wilayah::create(['nama_kabupaten' => 'Bandung']);
    $pengelola = User::factory()->create(['role' => 'pengelola']);

    $wisata = Wisata::create([
        'nama_wisata' => 'Kawah Putih',
        'deskripsi' => 'Deskripsi Kawah Putih',
        'lokasi' => 'Ciwidey',
        'harga_tiket' => 30000,
        'kategori' => 'Alam',
        'id_pengelola' => $pengelola->id,
        'id_wilayah' => $wilayah->id,
        'status' => 'disetujui_super_admin',
    ]);

    $tiket = Tiket::create([
        'kode_tiket' => 'TIK-TESTING-123',
        'user_id' => $user->id,
        'wisata_id' => $wisata->id,
        'jumlah_tiket' => 2,
        'total_harga' => 60000,
        'status_pembayaran' => 'pending',
        'status_tiket' => 'unused',
        'tanggal_kunjungan' => now()->format('Y-m-d'),
    ]);

    $response = $this->get('/api/tiket/TIK-TESTING-123/qrcode');

    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'image/svg+xml');
    
    expect($response->getContent())->toContain('<svg');
});

test('dapat mensimulasikan callback pembayaran tiket', function () {
    $user = User::factory()->create(['role' => 'user']);
    $wilayah = \App\Models\Wilayah::create(['nama_kabupaten' => 'Bandung']);
    $pengelola = User::factory()->create(['role' => 'pengelola']);

    $wisata = Wisata::create([
        'nama_wisata' => 'Kawah Putih',
        'deskripsi' => 'Deskripsi Kawah Putih',
        'lokasi' => 'Ciwidey',
        'harga_tiket' => 30000,
        'kategori' => 'Alam',
        'id_pengelola' => $pengelola->id,
        'id_wilayah' => $wilayah->id,
        'status' => 'disetujui_super_admin',
    ]);

    $tiket = Tiket::create([
        'kode_tiket' => 'TIK-SIMULASI-999',
        'user_id' => $user->id,
        'wisata_id' => $wisata->id,
        'jumlah_tiket' => 1,
        'total_harga' => 30000,
        'status_pembayaran' => 'pending',
        'status_tiket' => 'unused',
        'tanggal_kunjungan' => now()->format('Y-m-d'),
    ]);

    $response = $this->postJson('/api/payment/simulate-callback', [
        'kode_tiket' => 'TIK-SIMULASI-999',
        'status' => 'settlement'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Simulasi callback Midtrans berhasil diproses'
        ]);

    $this->assertDatabaseHas('tikets', [
        'kode_tiket' => 'TIK-SIMULASI-999',
        'status_pembayaran' => 'paid'
    ]);
});
