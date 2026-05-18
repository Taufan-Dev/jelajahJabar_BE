<?php

namespace App\Filament\Resources\Tikets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TiketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_tiket')
                    ->label('Kode Tiket')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->label('Pengunjung')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wisata.nama_wisata')
                    ->label('Wisata Tujuan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_tiket')
                    ->label('Jumlah')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('total_harga')
                    ->label('Total Bayar')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('status_pembayaran')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed', 'expired' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('status_tiket')
                    ->label('Status Tiket')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'info',
                        'used' => 'success',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('tanggal_kunjungan')
                    ->label('Tgl Kunjungan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_digunakan')
                    ->label('Waktu Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('ekspor_pdf')
                    ->label('Ekspor PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (\Filament\Tables\Table $table) {
                        $records = $table->getLivewire()->getFilteredTableQuery()->get();
                        
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.ticket-sales', [
                            'records' => $records,
                            'title' => 'Laporan Penjualan Tiket & Keuangan',
                            'user' => auth()->user(),
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'laporan-penjualan-tiket-' . now()->format('Y-m-d') . '.pdf'
                        );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('ekspor_terpilih_pdf')
                        ->label('Ekspor Terpilih ke PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.ticket-sales', [
                                'records' => $records,
                                'title' => 'Laporan Penjualan Tiket (Terpilih)',
                                'user' => auth()->user(),
                            ]);

                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                'laporan-penjualan-tiket-terpilih-' . now()->format('Y-m-d') . '.pdf'
                            );
                        }),
                ]),
            ]);
    }
}
