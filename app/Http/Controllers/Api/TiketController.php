<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TiketController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tiket milik user yang sedang terautentikasi
        $tikets = Tiket::where('user_id', $request->user()->id)
            ->with(['wisata.wilayah'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat tiket berhasil diambil',
            'data' => $tikets
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wisata_id' => 'required|exists:wisatas,id',
            'jumlah_tiket' => 'required|integer|min:1',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $wisata = Wisata::find($request->wisata_id);

        if ($wisata->status !== 'disetujui_super_admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tempat wisata ini sedang tidak aktif atau belum disetujui.'
            ], 400);
        }

        $user = $request->user();
        $totalHarga = $wisata->harga_tiket * $request->jumlah_tiket;
        
        // Generate kode tiket unik
        $kodeTiket = 'TIK-' . strtoupper(Str::random(8)) . '-' . now()->format('ymd');

        // Buat data tiket dengan status pembayaran pending
        $tiket = Tiket::create([
            'kode_tiket' => $kodeTiket,
            'user_id' => $user->id,
            'wisata_id' => $wisata->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga' => $totalHarga,
            'status_pembayaran' => 'pending',
            'status_tiket' => 'unused',
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
        ]);

        // INTEGRASI MIDTRANS
        // Set konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-xVxXb_Wc8P5y8JcW7v8X8V8X'); // Sandbox default
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $tiket->kode_tiket,
                'gross_amount' => (int) $tiket->total_harga,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => $wisata->id,
                    'price' => (int) $wisata->harga_tiket,
                    'quantity' => (int) $request->jumlah_tiket,
                    'name' => 'Tiket ' . $wisata->nama_wisata,
                ]
            ],
            'callbacks' => [
                'finish' => route('payment.success')
            ]
        ];

        $snapToken = null;
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Simpan snap token ke tiket
            $tiket->update([
                'snap_token' => $snapToken // Menampung token bayar jika perlu di mobile
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mendapatkan token Midtrans: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi tiket berhasil dibuat',
            'data' => [
                'tiket' => $tiket->load('wisata'),
                'snap_token' => $snapToken
            ]
        ], 201);
    }

    public function callback(Request $request)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-xVxXb_Wc8P5y8JcW7v8X8V8X');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notification = new \Midtrans\Notification();
            
            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id; // Ini kode_tiket kita
            $paymentType = $notification->payment_type;

            $tiket = Tiket::where('kode_tiket', $orderId)->with(['user', 'wisata'])->first();

            if (!$tiket) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tiket dengan kode tersebut tidak ditemukan.'
                ], 404);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                // Pembayaran berhasil!
                $tiket->update([
                    'status_pembayaran' => 'paid',
                ]);

                // KIRIM EMAIL & GENERATE PDF TIKET SECARA REAL-TIME (DEFERRED)
                defer(function () use ($tiket) {
                    try {
                        $user = $tiket->user;
                        $wisata = $tiket->wisata;

                        // Generate file PDF
                        $pdf = Pdf::loadView('reports.ticket-pdf', [
                            'tiket' => $tiket,
                            'wisata' => $wisata,
                            'user' => $user,
                        ]);

                        // Render beautiful HTML email template
                        $htmlContent = view('emails.ticket', [
                            'user' => $user,
                            'tiket' => $tiket,
                            'wisata' => $wisata,
                            'isSimulasi' => false
                        ])->render();

                        // Simpan sementara atau langsung kirim sebagai attachment
                        Mail::send([], [], function ($message) use ($user, $tiket, $pdf, $htmlContent) {
                            $message->to($user->email)
                                ->subject('E-Tiket Jelajah Jabar - ' . $tiket->wisata->nama_wisata)
                                ->html($htmlContent)
                                ->attachData($pdf->output(), 'e-tiket-' . $tiket->kode_tiket . '.pdf', [
                                    'mime' => 'application/pdf',
                                ]);
                        });

                        Log::info("Email tiket {$tiket->kode_tiket} berhasil dikirim ke {$user->email}");
                    } catch (\Exception $mailEx) {
                        Log::warning("Gagal mengirim email tiket {$tiket->kode_tiket}: " . $mailEx->getMessage());
                    }
                });

            } elseif ($transactionStatus == 'pending') {
                $tiket->update([
                    'status_pembayaran' => 'pending',
                ]);
            } else {
                // Deny, expire, cancel, dll.
                $tiket->update([
                    'status_pembayaran' => 'failed',
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Status pembayaran berhasil diproses'
            ]);

        } catch (\Exception $e) {
            Log::error('Error callback Midtrans: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Callback error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function qrCode($kodeTiket)
    {
        $tiket = Tiket::where('kode_tiket', $kodeTiket)->first();

        if (!$tiket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket tidak ditemukan'
            ], 404);
        }

        return response(QrCode::size(300)->margin(1)->generate($tiket->kode_tiket))
            ->header('Content-Type', 'image/svg+xml');
    }

    public function simulateCallback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_tiket' => 'required|exists:tikets,kode_tiket',
            'status' => 'nullable|in:settlement,pending,failed'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $tiket = Tiket::where('kode_tiket', $request->kode_tiket)->with(['user', 'wisata'])->first();
        $status = $request->input('status', 'settlement');

        if ($status === 'settlement') {
            $tiket->update([
                'status_pembayaran' => 'paid',
            ]);

            defer(function () use ($tiket) {
                try {
                    $user = $tiket->user;
                    $wisata = $tiket->wisata;

                    $pdf = Pdf::loadView('reports.ticket-pdf', [
                        'tiket' => $tiket,
                        'wisata' => $wisata,
                        'user' => $user,
                    ]);

                    // Render beautiful HTML email template
                    $htmlContent = view('emails.ticket', [
                        'user' => $user,
                        'tiket' => $tiket,
                        'wisata' => $wisata,
                        'isSimulasi' => true
                    ])->render();

                    Mail::send([], [], function ($message) use ($user, $tiket, $pdf, $htmlContent) {
                        $message->to($user->email)
                            ->subject('E-Tiket Jelajah Jabar - ' . $tiket->wisata->nama_wisata . ' (Simulasi)')
                            ->html($htmlContent)
                            ->attachData($pdf->output(), 'e-tiket-' . $tiket->kode_tiket . '.pdf', [
                                'mime' => 'application/pdf',
                            ]);
                    });

                    Log::info("Email tiket {$tiket->kode_tiket} berhasil disimulasikan ke {$user->email}");
                } catch (\Exception $mailEx) {
                    Log::warning("Gagal mengirim email tiket simulasian {$tiket->kode_tiket}: " . $mailEx->getMessage());
                }
            });
        } elseif ($status === 'pending') {
            $tiket->update([
                'status_pembayaran' => 'pending',
            ]);
        } else {
            $tiket->update([
                'status_pembayaran' => 'failed',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Simulasi callback Midtrans berhasil diproses',
            'data' => $tiket->fresh()
        ]);
    }
}
