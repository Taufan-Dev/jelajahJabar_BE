<?php

namespace App\Filament\Resources\Tikets;

use App\Filament\Resources\Tikets\Pages\CreateTiket;
use App\Filament\Resources\Tikets\Pages\EditTiket;
use App\Filament\Resources\Tikets\Pages\ListTikets;
use App\Filament\Resources\Tikets\Schemas\TiketForm;
use App\Filament\Resources\Tikets\Tables\TiketsTable;
use App\Models\Tiket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TiketResource extends Resource
{
    protected static ?string $model = Tiket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'kode_tiket';

    public static function form(Schema $schema): Schema
    {
        return TiketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TiketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'wisata']);
        $user = auth()->user();

        if ($user->role === 'pengelola') {
            return $query->whereHas('wisata', function ($q) use ($user) {
                $q->where('id_pengelola', $user->id);
            });
        }

        if ($user->role === 'admin_wilayah') {
            return $query->whereHas('wisata', function ($q) use ($user) {
                $q->where('id_wilayah', $user->id_wilayah);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTikets::route('/'),
            'create' => CreateTiket::route('/create'),
            'edit' => EditTiket::route('/{record}/edit'),
        ];
    }
}
