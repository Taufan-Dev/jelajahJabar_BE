<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // 2. Admin Wilayah (Misal untuk Kota Bandung id=1)
        User::create([
            'name' => 'Admin Bandung',
            'email' => 'adminbandung@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin_wilayah',
            'id_wilayah' => 1,
        ]);

        // 3. Pengelola Wisata
        User::create([
            'name' => 'Pengelola Kawah Putih',
            'email' => 'pengelolakawahputih@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);

        User::create([
            'name' => 'Pengelola Tangkuban Perahu',
            'email' => 'pengelolatangkuban@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);

        // Akun Wilayah & Pengelola Demo Kuningan
        User::create([
            'name' => 'Admin Kuningan',
            'email' => 'adminkuningan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin_wilayah',
            'id_wilayah' => 8, // Id wilayah Kabupaten Kuningan
        ]);

        User::create([
            'name' => 'Pengelola Waduk Darma',
            'email' => 'wadukdarma@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);

        User::create([
            'name' => 'Pengelola Linggajati',
            'email' => 'linggajati@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);

        User::create([
            'name' => 'Pengelola Waterland',
            'email' => 'waterland@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);

        User::create([
            'name' => 'Pengelola Kebun Raya',
            'email' => 'kebunraya@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);

        // 4. User Biasa (Pengunjung)
        User::create([
            'name' => 'User Pengunjung',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
