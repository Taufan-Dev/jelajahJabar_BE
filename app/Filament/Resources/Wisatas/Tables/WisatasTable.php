<?php

namespace App\Filament\Resources\Wisatas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class WisatasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('images/placeholder-wisata.jpg')),
                TextColumn::make('nama_wisata')
                    ->searchable(),
                TextColumn::make('harga_tiket')
                    ->numeric()
                    ->sortable()
                    ->money('IDR', locale: 'id'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'disetujui_admin' => 'info',
                        'disetujui_super_admin' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'disetujui_admin' => 'Disetujui Admin Wilayah',
                        'disetujui_super_admin' => 'Aktif',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('cetak_ranking')
                    ->label('Laporan Peringkat')
                    ->icon('heroicon-o-trophy')
                    ->color('warning')
                    ->action(function () {
                        $user = auth()->user();
                        
                        $query = \App\Models\Wisata::where('status', 'disetujui_super_admin')
                            ->withCount(['tikets as total_terjual' => function ($q) {
                                $q->where('status_pembayaran', 'paid');
                            }])
                            ->withAvg('rekomendasis as rata_rating', 'rating');

                        if ($user->role === 'admin_wilayah') {
                            $query->where('id_wilayah', $user->id_wilayah);
                        }

                        if ($user->role === 'pengelola') {
                            $query->where('id_pengelola', $user->id);
                        }

                        $records = $query->orderByDesc('total_terjual')
                            ->orderByDesc('rata_rating')
                            ->get();

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.tourism-ranking', [
                            'records' => $records,
                            'title' => 'Laporan Peringkat & Rekomendasi Wisata',
                            'user' => $user,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'laporan-peringkat-wisata-' . now()->format('Y-m-d') . '.pdf'
                        );
                    }),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('cek_status')
                    ->label('Cek Status')
                    ->icon('heroicon-o-information-circle')
                    ->color('info')
                    ->modalHeading('Status Persetujuan Wisata')
                    ->modalDescription(function (\App\Models\Wisata $record) {
                        if ($record->status === 'pending') {
                            return 'Wisata Anda saat ini masih berada di tahap validasi pertama dan sedang menunggu persetujuan dari Admin Wilayah.';
                        } elseif ($record->status === 'disetujui_admin') {
                            return 'Wisata Anda sudah disetujui oleh Admin Wilayah, dan kini sedang menunggu tahap validasi akhir oleh Super Admin.';
                        } else {
                            return 'Wisata Anda telah disetujui sepenuhnya oleh Super Admin dan kini aktif!';
                        }
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                \Filament\Actions\Action::make('setujui_admin')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (\App\Models\Wisata $record) => auth()->user()->role === 'admin_wilayah' && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan')
                    ->modalDescription('Apakah anda yakin menyetujuinya?')
                    ->action(function (\App\Models\Wisata $record) {
                        $record->update(['status' => 'disetujui_admin']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil Disetujui')
                            ->body("Wisata \"{$record->nama_wisata}\" telah disetujui di tingkat wilayah.")
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\Action::make('setujui_super')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (\App\Models\Wisata $record) => auth()->user()->role === 'super_admin' && $record->status === 'disetujui_admin')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Akhir')
                    ->modalDescription('Apakah anda yakin menyetujuinya?')
                    ->action(function (\App\Models\Wisata $record) {
                        $record->update(['status' => 'disetujui_super_admin']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil Disetujui')
                            ->body("Wisata \"{$record->nama_wisata}\" telah disetujui sepenuhnya.")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
