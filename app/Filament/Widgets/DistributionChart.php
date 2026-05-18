<?php

namespace App\Filament\Widgets;

use App\Models\Tiket;
use App\Models\Wisata;
use Filament\Widgets\ChartWidget;

class DistributionChart extends ChartWidget
{
    protected ?string $heading = 'Analisis Distribusi Data';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        if ($user->role === 'pengelola') {
            // Widget Pengelola: Distribusi Status Tiket (Paid vs Used) untuk wisatanya
            $wisataIds = Wisata::where('id_pengelola', $user->id)->pluck('id');
            
            $paidCount = Tiket::whereIn('wisata_id', $wisataIds)
                ->where('status_pembayaran', 'paid')
                ->where('status_tiket', 'paid')
                ->count();
                
            $usedCount = Tiket::whereIn('wisata_id', $wisataIds)
                ->where('status_pembayaran', 'paid')
                ->where('status_tiket', 'used')
                ->count();

            return [
                'datasets' => [
                    [
                        'label' => 'Status Tiket',
                        'data' => [$paidCount, $usedCount],
                        'backgroundColor' => ['#3B82F6', '#00bc7d'], // Blue vs Green
                    ],
                ],
                'labels' => ['Tiket Aktif (Belum Digunakan)', 'Tiket Digunakan (Check-in)'],
            ];
        }

        // Widget Super Admin & Admin Wilayah: Status Persetujuan Wisata
        $query = Wisata::query();
        if ($user->role === 'admin_wilayah') {
            $query->where('id_wilayah', $user->id_wilayah);
        }

        $pending = (clone $query)->where('status', 'pending')->count();
        $disetujuiAdmin = (clone $query)->where('status', 'disetujui_admin')->count();
        $disetujuiSuper = (clone $query)->where('status', 'disetujui_super_admin')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Persetujuan Wisata',
                    'data' => [$pending, $disetujuiAdmin, $disetujuiSuper],
                    'backgroundColor' => ['#F59E0B', '#3B82F6', '#00bc7d'], // Amber, Blue, Emerald
                ],
            ],
            'labels' => ['Menunggu Persetujuan', 'Disetujui Admin Wilayah', 'Disetujui Super Admin'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
