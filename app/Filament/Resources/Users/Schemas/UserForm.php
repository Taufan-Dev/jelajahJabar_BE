<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options(function () {
                        if (auth()->user()->role === 'admin_wilayah') {
                            return ['pengelola' => 'Pengelola'];
                        }
                        return [
                            'super_admin' => 'Super Admin',
                            'admin_wilayah' => 'Admin Wilayah',
                            'pengelola' => 'Pengelola',
                            'user' => 'User',
                        ];
                    })
                    ->default(fn () => auth()->user()->role === 'admin_wilayah' ? 'pengelola' : 'user')
                    ->required(),
                Select::make('id_wilayah')
                    ->label('Wilayah')
                    ->relationship('wilayah', 'nama_kabupaten')
                    ->default(fn () => auth()->user()->role === 'admin_wilayah' ? auth()->user()->id_wilayah : null)
                    ->disabled(fn () => auth()->user()->role === 'admin_wilayah')
                    ->dehydrated(),
            ]);
    }
}
