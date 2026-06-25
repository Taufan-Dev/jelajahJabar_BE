<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wilayahs = [
            ['nama_kabupaten' => 'Kota Bandung'],
            ['nama_kabupaten' => 'Kabupaten Bandung'],
            ['nama_kabupaten' => 'Kabupaten Bandung Barat'],
            ['nama_kabupaten' => 'Kota Cimahi'],
            ['nama_kabupaten' => 'Kabupaten Garut'],
            ['nama_kabupaten' => 'Kabupaten Bogor'],
            ['nama_kabupaten' => 'Kota Bogor'],
            ['nama_kabupaten' => 'Kabupaten Kuningan'],
            ['nama_kabupaten' => 'Kota Cirebon'],
        ];

        foreach ($wilayahs as $wilayah) {
            Wilayah::create($wilayah);
        }
    }
}
