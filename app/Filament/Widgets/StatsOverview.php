<?php

namespace App\Filament\Widgets;

use App\Models\Tiket;
use App\Models\User;
use App\Models\Wisata;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getPollingInterval(): ?string
    {
        return '5s';
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }
        
        if ($user->role === 'super_admin') {
            // Omset Seluruh Jabar (Hanya tiket dengan status_pembayaran = paid)
            $omset = Tiket::where('status_pembayaran', 'paid')->sum('total_harga');
            
            // Total Pengelola Terdaftar
            $totalPengelola = User::where('role', 'pengelola')->count();
            
            // Total Tiket Terjual
            $totalTiket = Tiket::where('status_pembayaran', 'paid')->sum('jumlah_tiket');
            
            return [
                Stat::make('Total Pendapatan Jabar', 'Rp ' . number_format($omset, 0, ',', '.'))
                    ->description('Seluruh tiket yang lunas')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('success'),
                Stat::make('Pengelola Terdaftar', $totalPengelola)
                    ->description('Akun pengelola wisata aktif')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('primary'),
                Stat::make('Total Tiket Terjual', $totalTiket)
                    ->description('Jumlah tiket lunas terjual')
                    ->descriptionIcon('heroicon-m-ticket')
                    ->color('info'),
            ];
        }

        if ($user->role === 'admin_wilayah') {
            // Wisata di wilayahnya
            $wisataIds = Wisata::where('id_wilayah', $user->id_wilayah)->pluck('id');
            
            // Omset Wilayah
            $omset = Tiket::whereIn('wisata_id', $wisataIds)
                ->where('status_pembayaran', 'paid')
                ->sum('total_harga');
                
            // Total Wisata Aktif
            $totalWisata = Wisata::where('id_wilayah', $user->id_wilayah)
                ->where('status', 'disetujui_super_admin')
                ->count();
                
            // Tiket Terjual
            $totalTiket = Tiket::whereIn('wisata_id', $wisataIds)
                ->where('status_pembayaran', 'paid')
                ->sum('jumlah_tiket');

            return [
                Stat::make('Pendapatan Wilayah', 'Rp ' . number_format($omset, 0, ',', '.'))
                    ->description('Lunas di wilayah Anda')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('success'),
                Stat::make('Wisata Aktif Wilayah', $totalWisata)
                    ->description('Telah disetujui super admin')
                    ->descriptionIcon('heroicon-m-map')
                    ->color('primary'),
                Stat::make('Tiket Terjual Wilayah', $totalTiket)
                    ->description('Jumlah tiket lunas terjual')
                    ->descriptionIcon('heroicon-m-ticket')
                    ->color('info'),
            ];
        }

        if ($user->role === 'pengelola') {
            // Wisata milik pengelola
            $wisataIds = Wisata::where('id_pengelola', $user->id)->pluck('id');
            
            // Omset Pengelola
            $omset = Tiket::whereIn('wisata_id', $wisataIds)
                ->where('status_pembayaran', 'paid')
                ->sum('total_harga');
                
            // Tiket Terjual
            $totalTiket = Tiket::whereIn('wisata_id', $wisataIds)
                ->where('status_pembayaran', 'paid')
                ->sum('jumlah_tiket');
                
            // Tiket Sudah Digunakan (Check-in)
            $totalUsed = Tiket::whereIn('wisata_id', $wisataIds)
                ->where('status_tiket', 'used')
                ->sum('jumlah_tiket');

            return [
                Stat::make('Pendapatan Saya', 'Rp ' . number_format($omset, 0, ',', '.'))
                    ->description('Omset lunas tempat wisata Anda')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('success'),
                Stat::make('Tiket Terjual', $totalTiket)
                    ->description('Jumlah tiket dibeli')
                    ->descriptionIcon('heroicon-m-ticket')
                    ->color('primary'),
                Stat::make('Pengunjung Check-In', $totalUsed)
                    ->description('Tiket yang sudah discan')
                    ->descriptionIcon('heroicon-m-check-badge')
                    ->color('info'),
            ];
        }

        return [];
    }
}
