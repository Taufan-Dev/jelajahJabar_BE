<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/payment/success', function (\Illuminate\Http\Request $request) {
    // HACK LOKAL: Ngrok versi gratis memblokir Webhook otomatis Midtrans.
    // Oleh karena itu, kita "mencegat" parameter yang dilempar Midtrans saat redirect kembali ke aplikasi (Finish URL)
    // untuk mengupdate status secara langsung.
    $orderId = $request->query('order_id');
    $status = $request->query('transaction_status');

    if ($orderId && ($status === 'capture' || $status === 'settlement')) {
        $tiket = \App\Models\Tiket::where('kode_tiket', $orderId)->with(['user', 'wisata'])->first();
        
        if ($tiket && $tiket->status_pembayaran === 'pending') {
            $tiket->update(['status_pembayaran' => 'paid']);

            // Kirim email tiket secara background agar loading page tidak lambat
            defer(function () use ($tiket) {
                try {
                    $user = $tiket->user;
                    $wisata = $tiket->wisata;
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.ticket-pdf', [
                        'tiket' => $tiket,
                        'wisata' => $wisata,
                        'user' => $user,
                    ]);
                    \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($user, $tiket, $pdf) {
                        $message->to($user->email)
                            ->subject('E-Tiket Jelajah Jabar - ' . $tiket->wisata->nama_wisata)
                            ->html("<p>Halo <strong>{$user->name}</strong>,</p><p>Pembayaran Anda berhasil! Berikut kami lampirkan E-Tiket PDF beserta QR Code Anda.</p><p>Terima kasih,<br><strong>Tim Jelajah Jabar</strong></p>")
                            ->attachData($pdf->output(), 'e-tiket-' . $tiket->kode_tiket . '.pdf', [
                                'mime' => 'application/pdf',
                            ]);
                    });
                    \Illuminate\Support\Facades\Log::info("Email tiket redirect {$tiket->kode_tiket} berhasil.");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Gagal email redirect: " . $e->getMessage());
                }
            });
        }
    }

    return view('payment.success');
})->name('payment.success');
