<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\LogValidasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidasiTiketController extends Controller
{
    public function validateTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_tiket' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        // ATURAN KEAMANAN: Validasi tiket hanya dapat dilakukan oleh user role pengelola
        if ($user->role !== 'pengelola') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pengelola wisata yang memiliki akses untuk melakukan validasi tiket.'
            ], 403);
        }

        // Cari tiket beserta detail wisata tujuannya
        $tiket = Tiket::where('kode_tiket', $request->kode_tiket)->with(['wisata', 'user'])->first();

        if (!$tiket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket tidak valid atau tidak terdaftar di sistem.'
            ], 444); // Kode khusus tiket not found
        }

        // ATURAN KEAMANAN: Validasi tiket hanya dapat dilakukan oleh pengelola wisata tujuan tersebut
        if ($tiket->wisata->id_pengelola !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket ini milik tempat wisata lain. Anda tidak diizinkan memvalidasinya.'
            ], 403);
        }

        // KEAMANAN: Tiket hanya dikirim/bisa digunakan setelah pembayaran lunas
        if ($tiket->status_pembayaran !== 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket belum dibayar! Status pembayaran saat ini: ' . strtoupper($tiket->status_pembayaran)
            ], 400);
        }

        // ATURAN KEAMANAN: QR tiket hanya dapat digunakan satu kali
        if ($tiket->status_tiket === 'used') {
            $log = LogValidasi::where('tiket_id', $tiket->id)->first();
            $waktuScan = $log ? $log->tanggal_validasi->translatedFormat('d M Y H:i') . ' WIB' : 'Waktu tidak tercatat';
            
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket sudah pernah digunakan sebelumnya pada ' . $waktuScan
            ], 423); // 423 Locked
        }

        // Update status tiket menjadi digunakan
        $tiket->update([
            'status_tiket' => 'used',
            'tanggal_digunakan' => now()
        ]);

        // Simpan log validasi tiket ke database
        LogValidasi::create([
            'tiket_id' => $tiket->id,
            'validated_by' => $user->id,
            'tanggal_validasi' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tiket VALID! Selamat datang di ' . $tiket->wisata->nama_wisata . '.',
            'data' => [
                'tiket' => $tiket,
                'pengunjung' => $tiket->user->name ?? 'N/A',
                'jumlah_tiket' => $tiket->jumlah_tiket
            ]
        ]);
    }
}
