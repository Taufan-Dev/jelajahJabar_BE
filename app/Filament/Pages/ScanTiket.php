<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Tiket;
use App\Models\LogValidasi;
use Filament\Notifications\Notification;
use BackedEnum;

class ScanTiket extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    
    protected static ?string $navigationLabel = 'Scan Tiket';
    
    protected static ?string $title = 'Scanner QR Tiket Pengunjung';

    protected string $view = 'filament.pages.scan-tiket';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'pengelola';
    }

    public function processScan($kodeTiket)
    {
        $tiket = Tiket::where('kode_tiket', $kodeTiket)->with('user')->first();

        if (!$tiket) {
            Notification::make()
                ->title('Tiket Tidak Ditemukan')
                ->body('Kode QR tidak valid atau tiket tidak ada dalam sistem.')
                ->danger()
                ->send();
            return;
        }

        // Pastikan tiket ini untuk wisata yang dikelola oleh user yang login
        $wisataDikelola = \App\Models\Wisata::where('id_pengelola', auth()->user()->id)->pluck('id')->toArray();
        if (!in_array($tiket->wisata_id, $wisataDikelola)) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Tiket ini bukan untuk tempat wisata yang Anda kelola.')
                ->danger()
                ->send();
            return;
        }

        // Pastikan tiket sudah dibayar
        if ($tiket->status_pembayaran !== 'paid') {
            Notification::make()
                ->title('Tiket Belum Dibayar')
                ->body('Status pembayaran tiket ini adalah: ' . $tiket->status_pembayaran)
                ->warning()
                ->send();
            return;
        }

        // Pastikan tiket belum digunakan
        if ($tiket->status_tiket === 'used') {
            Notification::make()
                ->title('Tiket Sudah Digunakan')
                ->body('Tiket ini sudah di-scan sebelumnya pada: ' . $tiket->tanggal_digunakan)
                ->danger()
                ->send();
            return;
        }

        // Validasi Berhasil
        $tiket->update([
            'status_tiket' => 'used',
            'tanggal_digunakan' => now(),
        ]);

        LogValidasi::create([
            'tiket_id' => $tiket->id,
            'validated_by' => auth()->user()->id,
            'tanggal_validasi' => now(),
        ]);

        Notification::make()
            ->title('Tiket Valid!')
            ->body("Pengunjung atas nama {$tiket->user->name} berhasil divalidasi untuk {$tiket->jumlah_tiket} orang.")
            ->success()
            ->send();
    }
}
