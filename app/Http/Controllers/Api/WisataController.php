<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WisataController extends Controller
{
    public function index(Request $request)
    {
        // Ambil wisata yang sudah disetujui sepenuhnya oleh Super Admin
        $query = Wisata::where('status', 'disetujui_super_admin')
            ->with(['wilayah', 'pengelola'])
            ->withCount(['tikets as total_terjual' => function ($q) {
                $q->where('status_pembayaran', 'paid');
            }])
            ->withAvg('rekomendasis as rata_rating', 'rating');

        // Filter berdasarkan Wilayah jika ada
        if ($request->has('id_wilayah')) {
            $query->where('id_wilayah', $request->id_wilayah);
        }

        // Filter berdasarkan Kategori jika ada
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Cari berdasarkan nama wisata
        if ($request->has('search')) {
            $query->where('nama_wisata', 'like', '%' . $request->search . '%');
        }

        // Algoritma Rekomendasi: Urutkan berdasarkan penjualan tiket terbanyak & rating rata-rata tertinggi
        $wisatas = $query->orderByDesc('total_terjual')
            ->orderByDesc('rata_rating')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Katalog wisata berhasil diambil',
            'data' => $wisatas
        ]);
    }

    public function show($id)
    {
        $wisata = Wisata::with(['wilayah', 'pengelola', 'rekomendasis.user'])
            ->withCount(['tikets as total_terjual' => function ($q) {
                $q->where('status_pembayaran', 'paid');
            }])
            ->withAvg('rekomendasis as rata_rating', 'rating')
            ->find($id);

        if (!$wisata) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wisata tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail wisata berhasil diambil',
            'data' => $wisata
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'pengelola') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pengelola wisata yang diizinkan menambahkan wisata.'
            ], 403);
        }

        $existingWisata = Wisata::where('id_pengelola', $request->user()->id)->first();
        
        if ($existingWisata) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah mendaftarkan wisata. Setiap pengelola hanya diizinkan mendaftarkan 1 wisata.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'nama_wisata' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string',
            'harga_tiket' => 'required|numeric|min:0',
            'kategori' => 'required|in:Alam,Budaya,Rekreasi,Edukasi',
            'id_wilayah' => 'required|exists:wilayahs,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('wisata', 'public');
        }

        $wisata = Wisata::create([
            'nama_wisata' => $request->nama_wisata,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'harga_tiket' => $request->harga_tiket,
            'kategori' => $request->kategori,
            'id_pengelola' => $request->user()->id,
            'id_wilayah' => $request->id_wilayah,
            'gambar' => $gambarPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Wisata berhasil didaftarkan dan sedang menunggu persetujuan admin.',
            'data' => $wisata
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $wisata = Wisata::find($id);

        if (!$wisata) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wisata tidak ditemukan'
            ], 404);
        }

        // Pastikan hanya pengelola pemilik wisata ini yang bisa mengupdate
        if ($wisata->id_pengelola !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk mengubah wisata ini.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_wisata' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string',
            'harga_tiket' => 'required|numeric|min:0',
            'kategori' => 'required|in:Alam,Budaya,Rekreasi,Edukasi',
            'id_wilayah' => 'required|exists:wilayahs,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('wisata', 'public');
            $wisata->gambar = $gambarPath;
        }

        $wisata->nama_wisata = $request->nama_wisata;
        $wisata->deskripsi = $request->deskripsi;
        $wisata->lokasi = $request->lokasi;
        $wisata->harga_tiket = $request->harga_tiket;
        $wisata->kategori = $request->kategori;
        $wisata->id_wilayah = $request->id_wilayah;
        
        // Setiap kali data diubah oleh pengelola, kembalikan status menjadi pending agar ditinjau ulang
        $wisata->status = 'pending';
        $wisata->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Wisata berhasil diperbarui dan status kembali pending menunggu verifikasi.',
            'data' => $wisata
        ]);
    }

    public function approveAdmin(Request $request, $id)
    {
        $wisata = Wisata::find($id);

        if (!$wisata) {
            return response()->json(['status' => 'error', 'message' => 'Wisata tidak ditemukan'], 404);
        }

        // Hak akses: admin_wilayah sewilayah
        $user = $request->user();
        if ($user->role !== 'admin_wilayah' || $user->id_wilayah !== $wisata->id_wilayah) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki wewenang untuk menyetujui wisata di wilayah ini.'
            ], 403);
        }

        if ($wisata->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Wisata harus berstatus pending untuk disetujui Admin Wilayah.'
            ], 400);
        }

        $wisata->status = 'disetujui_admin';
        $wisata->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Wisata berhasil disetujui oleh Admin Wilayah.',
            'data' => $wisata
        ]);
    }

    public function approveSuper(Request $request, $id)
    {
        $wisata = Wisata::find($id);

        if (!$wisata) {
            return response()->json(['status' => 'error', 'message' => 'Wisata tidak ditemukan'], 404);
        }

        // Hak akses: super_admin
        $user = $request->user();
        if ($user->role !== 'super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Super Admin yang diizinkan melakukan persetujuan akhir.'
            ], 403);
        }

        if ($wisata->status !== 'disetujui_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Wisata harus disetujui Admin Wilayah terlebih dahulu.'
            ], 400);
        }

        $wisata->status = 'disetujui_super_admin';
        $wisata->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Wisata berhasil disetujui sepenuhnya oleh Super Admin.',
            'data' => $wisata
        ]);
    }
}
