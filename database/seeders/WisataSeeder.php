<?php

namespace Database\Seeders;

use App\Models\Wisata;
use Illuminate\Database\Seeder;

class WisataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wisata::create([
            'nama_wisata' => 'Kawah Putih',
            'deskripsi' => 'Kawah Putih adalah sebuah danau kawah vulkanik yang terletak di Ciwidey, Jawa Barat. Danau ini memiliki air berwarna putih kehijauan yang indah.',
            'lokasi' => 'Ciwidey, Kabupaten Bandung',
            'harga_tiket' => 30000,
            'id_pengelola' => 3, // Asumsi id 3 adalah pengelola kawah putih
            'id_wilayah' => 2, // Asumsi id 2 adalah Kabupaten Bandung
            'status' => 'disetujui_super_admin',
        ]);

        Wisata::create([
            'nama_wisata' => 'Gunung Tangkuban Perahu',
            'deskripsi' => 'Gunung Tangkuban Parahu adalah salah satu gunung yang terletak di Provinsi Jawa Barat, Indonesia. Bentuknya menyerupai perahu terbalik.',
            'lokasi' => 'Lembang, Kabupaten Bandung Barat',
            'harga_tiket' => 35000,
            'id_pengelola' => 4, // Asumsi id 4 adalah pengelola tangkuban
            'id_wilayah' => 3, // Asumsi id 3 adalah Kabupaten Bandung Barat
            'status' => 'disetujui_admin', // Baru sampai tahap admin wilayah
        ]);

        Wisata::create([
            'nama_wisata' => 'Waduk Darma',
            'deskripsi' => 'Waduk Darma merupakan sebuah waduk yang terletak di sebelah barat daya kota Kuningan. Tempat ini sangat populer untuk menikmati panorama alam dan sunset.',
            'lokasi' => 'Darma, Kabupaten Kuningan',
            'harga_tiket' => 15000,
            'id_pengelola' => 6, // Pengelola Waduk Darma
            'id_wilayah' => 8, // Kabupaten Kuningan
            'status' => 'disetujui_super_admin',
        ]);

        Wisata::create([
            'nama_wisata' => 'Gedung Naskah Linggajati',
            'deskripsi' => 'Gedung Linggarjati adalah tempat diadakannya Perundingan Linggarjati antara Indonesia dan Belanda pada tahun 1946. Menjadi saksi sejarah kemerdekaan RI.',
            'lokasi' => 'Cilimus, Kabupaten Kuningan',
            'harga_tiket' => 10000,
            'id_pengelola' => 7, // Pengelola Linggajati
            'id_wilayah' => 8, // Kabupaten Kuningan
            'status' => 'pending', // Menunggu persetujuan Admin Wilayah
        ]);
    }
}
