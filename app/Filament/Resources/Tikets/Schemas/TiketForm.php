<?php

namespace App\Filament\Resources\Tikets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TiketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_tiket')
                    ->required(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('wisata_id')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah_tiket')
                    ->required()
                    ->numeric(),
                TextInput::make('total_harga')
                    ->required()
                    ->numeric(),
                Select::make('status_pembayaran')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'])
                    ->default('pending')
                    ->required(),
                Select::make('status_tiket')
                    ->options(['unused' => 'Unused', 'used' => 'Used'])
                    ->default('unused')
                    ->required(),
                DatePicker::make('tanggal_kunjungan')
                    ->required(),
                DateTimePicker::make('tanggal_digunakan'),
            ]);
    }
}
