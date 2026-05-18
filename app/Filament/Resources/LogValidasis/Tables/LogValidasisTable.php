<?php

namespace App\Filament\Resources\LogValidasis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LogValidasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tiket.kode_tiket')
                    ->label('Kode Tiket')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                TextColumn::make('tiket.user.name')
                    ->label('Pengunjung')
                    ->searchable(),
                TextColumn::make('tiket.wisata.nama_wisata')
                    ->label('Wisata Tujuan')
                    ->searchable(),
                TextColumn::make('tiket.jumlah_tiket')
                    ->label('Jumlah Pengunjung')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('validator.name')
                    ->label('Divalidasi Oleh')
                    ->sortable(),
                TextColumn::make('tanggal_validasi')
                    ->label('Waktu Validasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
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
                        
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.visitor-attendance', [
                            'records' => $records,
                            'title' => 'Laporan Kunjungan & Validasi Pengunjung',
                            'user' => auth()->user(),
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'laporan-validasi-kunjungan-' . now()->format('Y-m-d') . '.pdf'
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
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.visitor-attendance', [
                                'records' => $records,
                                'title' => 'Laporan Kunjungan & Validasi (Terpilih)',
                                'user' => auth()->user(),
                            ]);

                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                'laporan-validasi-terpilih-' . now()->format('Y-m-d') . '.pdf'
                            );
                        }),
                ]),
            ]);
    }
}
