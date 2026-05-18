<?php

namespace App\Filament\Resources\Rekomendasis\Pages;

use App\Filament\Resources\Rekomendasis\RekomendasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRekomendasi extends EditRecord
{
    protected static string $resource = RekomendasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
