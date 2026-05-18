<?php

namespace App\Filament\Resources\Rekomendasis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RekomendasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pengunjung')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wisata.nama_wisata')
                    ->label('Tempat Wisata')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => "★ " . number_format($state, 1))
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('ulasan')
                    ->label('Komentar/Feedback')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
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
                        
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.visitor-reviews', [
                            'records' => $records,
                            'title' => 'Laporan Ulasan & Feedback Pengunjung',
                            'user' => auth()->user(),
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'laporan-ulasan-pengunjung-' . now()->format('Y-m-d') . '.pdf'
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
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.visitor-reviews', [
                                'records' => $records,
                                'title' => 'Laporan Ulasan & Feedback (Terpilih)',
                                'user' => auth()->user(),
                            ]);

                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                'laporan-ulasan-terpilih-' . now()->format('Y-m-d') . '.pdf'
                            );
                        }),
                ]),
            ]);
    }
}
