<?php

namespace App\Filament\Resources\LogValidasis\Pages;

use App\Filament\Resources\LogValidasis\LogValidasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLogValidasi extends EditRecord
{
    protected static string $resource = LogValidasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
