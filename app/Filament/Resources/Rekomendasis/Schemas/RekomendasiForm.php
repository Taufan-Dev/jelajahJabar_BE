<?php

namespace App\Filament\Resources\Rekomendasis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class RekomendasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('wisata_id')
                    ->required()
                    ->numeric(),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                Textarea::make('ulasan')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('gambar')
                    ->label('Gambar Ulasan (Maksimal 5)')
                    ->image()
                    ->multiple()
                    ->maxFiles(5)
                    ->directory('review-images')
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }
}
