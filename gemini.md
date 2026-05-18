# GEMINI.md

# Project Name
Sistem Pemesanan Tiket dan Rekomendasi Wisata Jawa Barat

---

# Project Overview
Aplikasi mobile dan backend API untuk pemesanan tiket wisata di Jawa Barat dengan sistem rekomendasi wisata berdasarkan jumlah tiket terjual dan rating pengguna. Sistem menggunakan QR Code untuk validasi tiket di lokasi wisata dan payment gateway Midtrans untuk pembayaran online.

Backend menggunakan Laravel API dan Filament Admin Panel untuk pengelolaan data oleh admin dan pengelola wisata.

---

# Technology Stack

## Frontend
- Kotlin Android
- Retrofit
- RecyclerView
- Material Design

## Backend
- Laravel 12
- Laravel Sanctum
- REST API
- Filament Admin Panel

## Database
- MySQL

## Payment Gateway
- Midtrans

## Additional Libraries
- DomPDF
- Simple QRCode
- Laravel Mail

---

# User Roles

## Super Admin
- Kelola semua data
- Approve final wisata
- Melihat seluruh laporan
- Mengelola wilayah
- Mengelola semua user
- Melihat ranking wisata

## Admin Wilayah
- Mengelola wisata berdasarkan wilayah
- Validasi wisata dari pengelola wisata
- Melihat laporan wilayah
- Menambahkan user pengelola wisata

## Pengelola Wisata
- Registrasi akun
- Mengelola wisata
- Menambahkan wisata
- Validasi tiket pengunjung menggunakan QR Scanner

## User
- Registrasi dan login
- Melihat daftar wisata
- Membeli tiket wisata
- Pembayaran online menggunakan Midtrans
- Menerima tiket PDF melalui email
- Memberikan ulasan dan rating wisata

---

# Main Features

## Authentication
- Register
- Login
- Logout
- Role-based authentication

## Wisata
- CRUD wisata
- Approval wisata bertingkat
- Data wisata berdasarkan wilayah

## Ticketing
- Pembelian tiket
- Generate transaksi
- Generate PDF tiket
- Generate QR Code tiket
- Email tiket otomatis

## Payment
- Midtrans integration
- Payment callback
- Payment verification

## Recommendation System
- Rating wisata
- Review wisata
- Ranking wisata otomatis

## Ticket Validation
- Scan QR tiket
- Validasi tiket
- Tiket hanya dapat digunakan satu kali

## Admin Panel (Filament)
- Dashboard statistik
- CRUD wisata
- CRUD wilayah
- CRUD user
- Approval wisata
- Monitoring tiket
- Monitoring pembayaran
- Monitoring validasi tiket

---

# Filament Panel Access

## Super Admin Panel
Akses penuh terhadap:
- Semua wisata
- Semua user
- Semua laporan
- Semua wilayah
- Ranking wisata

## Admin Wilayah Panel
Akses terbatas:
- Wisata berdasarkan wilayah
- Validasi wisata
- Laporan wilayah
- Pengelola wisata wilayah

---

# Wisata Approval Flow

Pengelola Wisata
↓
Tambah Wisata
↓
Status: pending
↓
Admin Wilayah Approve
↓
Status: disetujui_admin
↓
Super Admin Approve
↓
Status: disetujui_super_admin

---

# Ticket Purchase Flow

User memilih wisata
↓
User membeli tiket
↓
Sistem membuat transaksi pending
↓
Midtrans payment process
↓
Midtrans callback
↓
Status pembayaran menjadi paid
↓
Generate PDF Ticket
↓
Generate QR Code
↓
Kirim tiket ke email user

---

# Ticket Validation Flow

Pengunjung datang ke lokasi wisata
↓
Pengelola wisata scan QR tiket
↓
Aplikasi mengirim request ke API
↓
Backend memvalidasi tiket
↓
Jika valid:
- tiket berubah menjadi used
- pengunjung dapat masuk

---

# Recommendation Algorithm

Wisata akan diurutkan berdasarkan:
- Jumlah tiket terjual
- Rating rata-rata pengguna

Semakin tinggi penjualan dan rating, semakin tinggi posisi wisata pada halaman rekomendasi.

---

# Database Tables

## users
- id
- name
- email
- password
- role
- id_wilayah

## wilayah
- id
- nama_kabupaten

## wisata
- id
- nama_wisata
- deskripsi
- lokasi
- harga_tiket
- id_pengelola
- id_wilayah
- status

## tiket
- id
- kode_tiket
- user_id
- wisata_id
- jumlah_tiket
- total_harga
- status_pembayaran
- status_tiket
- tanggal_kunjungan
- tanggal_digunakan

## rekomendasi
- id
- user_id
- wisata_id
- rating
- ulasan

## log_validasi
- id
- tiket_id
- validated_by
- tanggal_validasi

---

# API Endpoints

## Authentication
POST /api/register
POST /api/login
POST /api/logout

## Wisata
GET /api/wisata
GET /api/wisata/{id}
POST /api/wisata
PUT /api/wisata/{id}

## Approval
POST /api/wisata/{id}/approve-admin
POST /api/wisata/{id}/approve-super

## Tiket
POST /api/tiket
GET /api/tiket

## Payment
POST /api/payment
POST /api/payment/callback

## Review
POST /api/rekomendasi

## Ticket Validation
POST /api/validasi-tiket

---

# Email Ticket System

Setelah pembayaran berhasil:
- Sistem generate PDF tiket
- Sistem generate QR Code
- Tiket otomatis dikirim ke email user

PDF tiket berisi:
- Nama wisata
- Nama pengunjung
- Jumlah tiket
- QR Code
- Kode tiket unik

---

# Security Rules

- Tiket hanya dikirim setelah pembayaran berhasil
- QR tiket hanya dapat digunakan satu kali
- Validasi tiket hanya dapat dilakukan oleh pengelola wisata
- User hanya dapat memberi review setelah membeli tiket
- Wisata hanya tampil jika sudah disetujui super admin

---

# Future Development

- AI recommendation system
- Real-time dashboard analytics
- Push notification
- Multi-language support
- Visitor statistics dashboard
- Ticket scanning history
- Dynamic QR security