<?php

namespace App\Filament\Resources\Rekomendasis;

use App\Filament\Resources\Rekomendasis\Pages\CreateRekomendasi;
use App\Filament\Resources\Rekomendasis\Pages\EditRekomendasi;
use App\Filament\Resources\Rekomendasis\Pages\ListRekomendasis;
use App\Filament\Resources\Rekomendasis\Schemas\RekomendasiForm;
use App\Filament\Resources\Rekomendasis\Tables\RekomendasisTable;
use App\Models\Rekomendasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RekomendasiResource extends Resource
{
    protected static ?string $model = Rekomendasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return RekomendasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RekomendasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
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
            'index' => ListRekomendasis::route('/'),
            'create' => CreateRekomendasi::route('/create'),
            'edit' => EditRekomendasi::route('/{record}/edit'),
        ];
    }
}
