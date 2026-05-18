<?php

namespace App\Filament\Resources\Wilayahs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WilayahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kabupaten')
                    ->required(),
            ]);
    }
}
