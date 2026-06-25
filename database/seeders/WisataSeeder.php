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
            'deskripsi' => 'Kawah Putih adalah sebuah danau kawah vulkanik yang terletak di Ciwidey, Jawa Barat. Danau ini memiliki air berwarna putih kehijauan yang indah akibat kandungan belerangnya yang tinggi. Udara di sekitarnya sangat dingin dan segar, menjadikannya destinasi favorit untuk melepas penat dan berswafoto.',
            'lokasi' => 'Ciwidey, Kabupaten Bandung',
            'harga_tiket' => 30000,
            'kategori' => 'Alam',
            'id_pengelola' => 3, // Pengelola Kawah Putih
            'id_wilayah' => 2, // Kabupaten Bandung
            'status' => 'disetujui_super_admin',
            'gambar' => [
                'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?q=80&w=800',
                'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800',
                'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?q=80&w=800',
                'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=800',
                'https://images.unsplash.com/photo-1472214222541-d510753a8707?q=80&w=800'
            ]
        ]);

        Wisata::create([
            'nama_wisata' => 'Gunung Tangkuban Perahu',
            'deskripsi' => 'Gunung Tangkuban Parahu adalah salah satu gunung aktif yang terkenal di Jawa Barat. Bentuk puncaknya yang menyerupai perahu terbalik erat kaitannya dengan legenda Sangkuriang. Pengunjung dapat menikmati pemandangan Kawah Ratu yang luas dengan kawah belerang yang mengepul aktif.',
            'lokasi' => 'Lembang, Kabupaten Bandung Barat',
            'harga_tiket' => 35000,
            'kategori' => 'Alam',
            'id_pengelola' => 4, // Pengelola Tangkuban Perahu
            'id_wilayah' => 3, // Kabupaten Bandung Barat
            'status' => 'disetujui_super_admin',
            'gambar' => [
                'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=800',
                'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800',
                'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?q=80&w=800',
                'https://images.unsplash.com/photo-1486873249359-2731bd6dafc7?q=80&w=800',
                'https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=800'
            ]
        ]);

        Wisata::create([
            'nama_wisata' => 'Waduk Darma',
            'deskripsi' => 'Waduk Darma merupakan sebuah bendungan luas yang menawarkan panorama alam pegunungan yang asri di Kuningan. Tempat ini sangat digemari untuk bersantai, memancing, berkeliling dengan perahu wisata, dan menikmati panorama matahari terbenam yang memukau di sore hari.',
            'lokasi' => 'Darma, Kabupaten Kuningan',
            'harga_tiket' => 15000,
            'kategori' => 'Rekreasi',
            'id_pengelola' => 6, // Pengelola Waduk Darma
            'id_wilayah' => 8, // Kabupaten Kuningan
            'status' => 'disetujui_super_admin',
            'gambar' => [
                'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800',
                'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=800',
                'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800',
                'https://images.unsplash.com/photo-1433832597046-4f10e10ac764?q=80&w=800',
                'https://images.unsplash.com/photo-1518495973542-4542c06a5843?q=80&w=800'
            ]
        ]);

        Wisata::create([
            'nama_wisata' => 'Gedung Naskah Linggajati',
            'deskripsi' => 'Gedung Linggarjati adalah gedung bersejarah tempat diadakannya Perundingan Linggarjati antara delegasi Indonesia dan Belanda pada November 1946. Terletak di kaki Gunung Ciremai, gedung berarsitektur kolonial ini dikelilingi taman luas yang asri dan sejuk, menyimpan diorama bersejarah kemerdekaan Indonesia.',
            'lokasi' => 'Cilimus, Kabupaten Kuningan',
            'harga_tiket' => 10000,
            'kategori' => 'Budaya',
            'id_pengelola' => 7, // Pengelola Linggajati
            'id_wilayah' => 8, // Kabupaten Kuningan
            'status' => 'disetujui_super_admin',
            'gambar' => [
                'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=800',
                'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=800',
                'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=800',
                'https://images.unsplash.com/photo-1582407947304-fd86f028f716?q=80&w=800',
                'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=800'
            ]
        ]);

        Wisata::create([
            'nama_wisata' => 'Cirebon Waterland Ade Irma Suryani',
            'deskripsi' => 'Cirebon Waterland adalah taman rekreasi air modern yang berada tepat di tepi Pantai Cirebon. Menyediakan berbagai kolam renang tematik, seluncuran air yang seru, restoran bernuansa kapal pinisi, dan cottage kayu eksotik di atas permukaan laut yang sangat indah.',
            'lokasi' => 'Lemahwungkuk, Kota Cirebon',
            'harga_tiket' => 25000,
            'kategori' => 'Rekreasi',
            'id_pengelola' => 8, // Pengelola Waterland
            'id_wilayah' => 9, // Kota Cirebon
            'status' => 'disetujui_super_admin',
            'gambar' => [
                'https://images.unsplash.com/photo-1582650625119-3a31f8fa2699?q=80&w=800',
                'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=800',
                'https://images.unsplash.com/photo-1570129477492-45c003edd2be?q=80&w=800',
                'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=800',
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=800'
            ]
        ]);

        Wisata::create([
            'nama_wisata' => 'Kebun Raya Bogor',
            'deskripsi' => 'Kebun Raya Bogor adalah kebun botani tertua di Asia Tenggara yang memiliki koleksi puluhan ribu jenis pohon dan tumbuhan langka. Tempat ini menawarkan hamparan rumput hijau yang luas, pemandangan Danau Gunting dengan Istana Kepresidenan Bogor di latar belakang, serta pohon-pohon raksasa bersejarah.',
            'lokasi' => 'Bogor Tengah, Kota Bogor',
            'harga_tiket' => 20000,
            'kategori' => 'Edukasi',
            'id_pengelola' => 9, // Pengelola Kebun Raya
            'id_wilayah' => 7, // Kota Bogor
            'status' => 'disetujui_super_admin',
            'gambar' => [
                'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?q=80&w=800',
                'https://images.unsplash.com/photo-1448375240586-882707db888b?q=80&w=800',
                'https://images.unsplash.com/photo-1502082553048-f009c37129b9?q=80&w=800',
                'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?q=80&w=800',
                'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?q=80&w=800'
            ]
        ]);
    }
}
