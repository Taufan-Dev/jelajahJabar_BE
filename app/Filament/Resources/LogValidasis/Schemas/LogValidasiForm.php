<?php

namespace App\Filament\Resources\LogValidasis\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LogValidasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tiket_id')
                    ->required()
                    ->numeric(),
                TextInput::make('validated_by')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('tanggal_validasi')
                    ->required(),
            ]);
    }
}
