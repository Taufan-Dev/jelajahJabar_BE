# 🗺️ Jelajah Jabar - Sistem Pemesanan Tiket & Rekomendasi Wisata Jawa Barat

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![Filament Version](https://img.shields.io/badge/Filament-3.x-yellow.svg?style=flat-square&logo=laravel)](https://filamentphp.com)
[![Midtrans](https://img.shields.io/badge/Payment-Midtrans_Sandbox-blue.svg?style=flat-square)](https://midtrans.com)
[![Pest Testing](https://img.shields.io/badge/Testing-Pest_PHP-green.svg?style=flat-square)](https://pestphp.com)

**Jelajah Jabar** adalah platform backend API dan Panel Administrasi modern untuk mengelola pemesanan tiket, validasi QR-Code masuk wisata, serta sistem rekomendasi destinasi wisata di Jawa Barat berdasarkan volume tiket terjual dan ulasan rating bintang pengunjung.

---

## 🚀 Fitur Utama & Keunggulan

1.  **🎛️ Multi-Role Panel Administrasi (Filament v3)**:
    - **Super Admin**: Akses kontrol penuh wilayah, user, persetujuan wisata akhir, dashboard statistik terpusat, dan ekspor laporan PDF.
    - **Admin Wilayah**: Mengelola dan menyetujui wisata di wilayah kabupaten kerjanya, melihat laporan, serta mendaftarkan akun pengelola wisata setempat.
    - **Pengelola Wisata**: Mendaftarkan destinasi wisata tunggal, melakukan pemindaian (scan) QR-Code tiket masuk secara real-time.
2.  **💳 Integrasi Payment Gateway Midtrans (Snap)**: Pembayaran tiket online aman melalui QRIS, Transfer Bank (VA), atau E-Wallet langsung di dalam aplikasi handphone.
3.  **📧 Tiket PDF Otomatis & QR-Code offline**: Setelah transaksi lunas melalui webhook Midtrans, sistem otomatis mengirimkan E-Tiket PDF berdesain klasik dengan QR-Code tersemat langsung ke email pengunjung.
4.  **🏆 Algoritma Rekomendasi Pintar**: Mengurutkan tempat wisata terbaik secara otomatis berdasarkan volume tiket terjual dan akumulasi rata-rata rating bintang ulasan.
5.  **📄 4 Laporan PDF Formal Agensi**:
    - Laporan Penjualan Tiket & Keuangan (Utama)
    - Laporan Kehadiran Kunjungan Fisik (Scan QR)
    - Laporan Peringkat & Rekomendasi Wisata Terpopuler
    - Laporan Ulasan & Feedback Kritik/Saran Pengunjung

---

## 🛠️ Tech Stack

- **Framework Core**: Laravel 12 & Laravel Sanctum
- **Admin Panel Dashboard**: Filament Admin v3
- **Database**: MySQL
- **Payment Gateway**: Midtrans PHP SDK
- **PDF Generator**: Barryvdh DomPDF
- **QR-Code Generator**: Simple QRCode (Offline Base64 embedding)
- **Testing**: Pest PHP 3.x

---

## 💻 Panduan Instalasi Cepat

Ikuti langkah berikut untuk menjalankan backend ini di server lokal Anda:

### 1. Kloning & Masuk ke Workspace

```bash
git clone <repository-url> wisata-backend
cd wisata-backend
```

### 2. Instal Dependensi Composer

```bash
composer install
```

### 3. Jalankan Migrasi & Seed Data Demo (Kabupaten Kuningan)

Kami menyediakan data demo lengkap untuk Kabupaten Kuningan agar memudahkan presentasi/pengujian:

```bash
php artisan migrate:fresh --seed
```

### 4. Buat Symlink Storage Gambar Wisata & Jalankan Server

```bash
php artisan storage:link
php artisan serve
```

Aplikasi backend kini berjalan aktif di `http://127.0.0.1:8000`.

---

## 🔑 Akun Demo Dashboard Administrasi

Buka tautan `http://127.0.0.1:8000/admin/login` di browser Anda:

| Role Akun                  | Email Login               | Sandi Default | Hak Akses Utama                                           |
| :------------------------- | :------------------------ | :------------ | :-------------------------------------------------------- |
| **Super Admin**            | `superadmin@gmail.com`    | `password`    | Kendali Penuh Global, Dashboard Utama & Cetak Semua PDF   |
| **Admin Wilayah Kuningan** | `adminkuningan@gmail.com` | `password`    | Hanya wilayah Kab. Kuningan, Approval Wisata Level 1      |
| **Pengelola Waduk Darma**  | `wadukdarma@gmail.com`    | `password`    | Mengelola Wisata Waduk Darma, Scan QR Validasi Pengunjung |
| **Pengelola Linggajati**   | `linggajati@gmail.com`    | `password`    | Mengelola Gedung Naskah Linggajati (Status Pending)       |

---

## 📡 Dokumentasi Endpoint REST API

Semua request wajib menyertakan header `Accept: application/json`. Endpoint terproteksi memerlukan header `Authorization: Bearer <your_token>`.

### 1. Autentikasi & Profil (Fase 1)

- **POST** `/api/register` | Pendaftaran akun Pengunjung umum baru.
    - _Body_: `name`, `email`, `password`
- **POST** `/api/login` | Masuk untuk mendapatkan Token Sanctum.
    - _Body_: `email`, `password`
- **POST** `/api/logout` | Menghapus token aktif secara aman. _(Terproteksi)_
- **GET** `/api/me` | Mengambil detail profil user beserta data wilayah kerja kerabat. _(Terproteksi)_

### 2. Katalog Wisata & Rekomendasi (Fase 2)

- **GET** `/api/wisata` | Mengambil katalog wisata aktif.
    - _Parameter Opsional_: `id_wilayah`, `search` (cari nama wisata)
    - _Algoritma_: Otomatis mengurutkan rekomendasi dari **Tiket Terjual Terbanyak** & **Rata-rata Rating Bintang Tertinggi**.
- **GET** `/api/wisata/{id}` | Mengambil detail destinasi wisata beserta kompilasi review pengunjung.
- **POST** `/api/wisata` | Pengelola mendaftarkan wisata baru (Status awal: pending). _(Terproteksi - Pengelola)_
    - _Body_: `nama_wisata`, `deskripsi`, `lokasi`, `harga_tiket`, `id_wilayah`, `gambar` (File Foto)
- **POST** `/api/wisata/{id}` | Memperbarui wisata. Status otomatis kembali _pending_ jika diubah. _(Terproteksi - Pengelola)_
    - _Body_: Sama seperti store (mendukung unggahan gambar baru)

### 3. Persetujuan Bertingkat Wisata

- **POST** `/api/wisata/{id}/approve-admin` | Admin Wilayah menyetujui wisata di kawasannya (Status: `disetujui_admin`). _(Terproteksi - Admin Wilayah)_
- **POST** `/api/wisata/{id}/approve-super` | Super Admin menyetujui wisata sepenuhnya (Status: `disetujui_super_admin` / Aktif). _(Terproteksi - Super Admin)_

### 4. Transaksi Tiket & Midtrans Payment

- **POST** `/api/tiket` | Membuat reservasi tiket & mendapatkan token bayar Midtrans Snap. _(Terproteksi - User)_
    - _Body_: `wisata_id`, `jumlah_tiket`, `tanggal_kunjungan`
    - _Respons_: Mengembalikan detail tiket lengkap beserta **`snap_token`** untuk pembayaran di mobile.
- **GET** `/api/tiket` | Mengambil riwayat pembelian tiket user bersangkutan. _(Terproteksi - User)_
- **POST** `/api/payment/callback` | Webhook publik menerima status bayar dari Midtrans. Otomatis melunasi tiket, membuat QR-Code, dan mengirimkan file PDF Tiket ke email pembeli. _(Umum)_

### 5. Rating & Ulasan (Feedback)

- **POST** `/api/rekomendasi` | Mengirim rating & review tempat wisata. _(Terproteksi - User)_
    - _Syarat_: Hanya bisa mengirim jika user **pernah membeli tiket lunas** untuk tempat wisata terkait.
    - _Body_: `wisata_id`, `rating` (1 s.d 5), `ulasan`, `gambar[]` (Maksimal 5 berkas foto ulasan visual)

### 6. Pintu Masuk / Validasi QR-Code

- **POST** `/api/validasi-tiket` | Scan QR-Code masuk oleh petugas pintu gerbang wisata. _(Terproteksi - Pengelola)_
    - _Body_: `kode_tiket`
    - _Keamanan_: Tiket hanya dapat di-scan 1 kali. Scan kedua kalinya akan ditolak dan menampilkan info waktu/tanggal penggunaan pertamanya demi mencegah duplikasi.

---

## 🧪 Pengujian Otomatis (Automated Testing)

Gunakan **Pest PHP** untuk menjalankan semua pengujian endpoint API guna memastikan kestabilan kode:

```bash
php artisan test
```

Atau jika ingin memfilter pengujian autentikasi saja:

```bash
php artisan test --filter=AuthApiTest
```

---

_Dibuat dengan 💚 untuk Pariwisata Jawa Barat yang Lebih Maju dan Digital._
