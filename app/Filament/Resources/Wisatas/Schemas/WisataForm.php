<?php

namespace App\Filament\Resources\Wisatas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class WisataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_wisata')
                    ->required(),
                FileUpload::make('gambar')
                    ->label('Gambar Utama Wisata')
                    ->image()
                    ->multiple()
                    ->directory('wisata-images')
                    ->maxSize(5120) // 5MB
                    ->nullable(),
                Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('lokasi')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('harga_tiket')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Select::make('kategori')
                    ->options([
                        'Alam' => 'Alam',
                        'Budaya' => 'Budaya',
                        'Rekreasi' => 'Rekreasi',
                        'Edukasi' => 'Edukasi',
                    ])
                    ->default('Alam')
                    ->required(),
                Select::make('id_pengelola')
                    ->label('Pengelola')
                    ->relationship('pengelola', 'name')
                    ->disabled(fn () => auth()->user()->role === 'pengelola')
                    ->required(),
                Select::make('id_wilayah')
                    ->label('Wilayah')
                    ->relationship('wilayah', 'nama_kabupaten')
                    ->disabled(fn () => auth()->user()->role !== 'super_admin')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'disetujui_admin' => 'Disetujui Admin Wilayah',
                        'disetujui_super_admin' => 'Disetujui Super Admin',
                    ])
                    ->disabled()
                    ->default('pending')
                    ->required(),
            ]);
    }
}
