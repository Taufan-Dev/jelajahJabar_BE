<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rekomendasi;
use App\Models\Tiket;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RekomendasiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wisata_id' => 'required|exists:wisatas,id',
            'rating' => 'required|numeric|min:1|max:5',
            'ulasan' => 'required|string',
            'gambar' => 'nullable|array|max:5',
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        // ATURAN KEAMANAN: User hanya dapat memberi ulasan setelah membeli tiket wisata tersebut
        $hasBoughtTicket = Tiket::where('user_id', $user->id)
            ->where('wisata_id', $request->wisata_id)
            ->where('status_pembayaran', 'paid')
            ->exists();

        if (!$hasBoughtTicket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda hanya diperbolehkan memberikan ulasan setelah sukses membeli tiket wisata ini.'
            ], 403);
        }

        // Cek apakah user sudah pernah memberi ulasan untuk wisata ini sebelumnya
        $existingReview = Rekomendasi::where('user_id', $user->id)
            ->where('wisata_id', $request->wisata_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah pernah memberikan ulasan untuk tempat wisata ini.'
            ], 400);
        }

        // Proses upload multi-gambar (maksimal 5)
        $uploadedImages = [];
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $image) {
                $path = $image->store('rekomendasi', 'public');
                $uploadedImages[] = $path;
            }
        }

        // Simpan ulasan ke database
        $rekomendasi = Rekomendasi::create([
            'user_id' => $user->id,
            'wisata_id' => $request->wisata_id,
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
            'gambar' => $uploadedImages, // Akan otomatis ter-cast menjadi JSON array di database
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan Anda berhasil dikirim, terima kasih atas masukannya!',
            'data' => $rekomendasi
        ], 201);
    }
}
