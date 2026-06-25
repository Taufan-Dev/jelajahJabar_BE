<?php

namespace App\Filament\Resources\LogValidasis;

use App\Filament\Resources\LogValidasis\Pages\CreateLogValidasi;
use App\Filament\Resources\LogValidasis\Pages\EditLogValidasi;
use App\Filament\Resources\LogValidasis\Pages\ListLogValidasis;
use App\Filament\Resources\LogValidasis\Schemas\LogValidasiForm;
use App\Filament\Resources\LogValidasis\Tables\LogValidasisTable;
use App\Models\LogValidasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogValidasiResource extends Resource
{
    protected static ?string $model = LogValidasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return LogValidasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogValidasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['tiket.user', 'tiket.wisata', 'validator']);
        $user = auth()->user();

        if ($user->role === 'pengelola') {
            return $query->whereHas('tiket.wisata', function ($q) use ($user) {
                $q->where('id_pengelola', $user->id);
            });
        }

        if ($user->role === 'admin_wilayah') {
            return $query->whereHas('tiket.wisata', function ($q) use ($user) {
                $q->where('id_wilayah', $user->id_wilayah);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogValidasis::route('/'),
            'create' => CreateLogValidasi::route('/create'),
            'edit' => EditLogValidasi::route('/{record}/edit'),
        ];
    }
}
