<?php

namespace App\Filament\Resources\LogValidasis\Pages;

use App\Filament\Resources\LogValidasis\LogValidasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLogValidasis extends ListRecords
{
    protected static string $resource = LogValidasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
