<?php

namespace App\Filament\Widgets;

use App\Models\Tiket;
use App\Models\Wisata;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TicketSalesChart extends ChartWidget
{
    protected ?string $heading = 'Tren Penjualan Tiket';
    
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected function getFilters(): ?array
    {
        return [
            'monthly' => 'Bulanan (Tahun Ini)',
            'daily' => 'Harian (30 Hari Terakhir)',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'monthly';
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        // Scope wisata berdasarkan role
        $query = Tiket::where('status_pembayaran', 'paid');
        
        if ($user->role === 'pengelola') {
            $wisataIds = Wisata::where('id_pengelola', $user->id)->pluck('id');
            $query->whereIn('wisata_id', $wisataIds);
        } elseif ($user->role === 'admin_wilayah') {
            $wisataIds = Wisata::where('id_wilayah', $user->id_wilayah)->pluck('id');
            $query->whereIn('wisata_id', $wisataIds);
        }

        if ($activeFilter === 'daily') {
            // Harian (30 Hari Terakhir)
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $tickets = $query->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, SUM(jumlah_tiket) as total')
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $labels = [];
            $data = [];

            for ($i = 29; $i >= 0; $i--) {
                $dateString = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::now()->subDays($i)->isoFormat('D MMM');
                $data[] = (int)($tickets[$dateString] ?? 0);
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Tiket Terjual (Harian)',
                        'data' => $data,
                        'borderColor' => '#00bc7d',
                        'backgroundColor' => 'rgba(0, 188, 125, 0.1)',
                        'fill' => true,
                    ],
                ],
                'labels' => $labels,
            ];
        }

        // Bulanan (Tahun Ini) - Default
        $year = Carbon::now()->year;
        $tickets = $query->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(jumlah_tiket) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $data[] = (int)($tickets[$m] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tiket Terjual (Bulanan)',
                    'data' => $data,
                    'borderColor' => '#00bc7d',
                    'backgroundColor' => 'rgba(0, 188, 125, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
