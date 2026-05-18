<?php

namespace App\Filament\Pages\Auth;

use App\Models\Wilayah;
use App\Models\Wisata;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class RegisterPengelola extends BaseRegister
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                
                // Fields for Wisata
                TextInput::make('nama_wisata')
                    ->label('Nama Wisata')
                    ->required()
                    ->maxLength(255),
                    
                Select::make('id_wilayah')
                    ->label('Wilayah Operasional')
                    ->options(Wilayah::all()->pluck('nama_kabupaten', 'id'))
                    ->required()
                    ->searchable(),
                    
                Textarea::make('deskripsi')
                    ->label('Deskripsi Singkat Wisata')
                    ->required(),
                    
                TextInput::make('lokasi')
                    ->label('Alamat Lengkap Lokasi')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('harga_tiket')
                    ->label('Harga Tiket (Rupiah)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),
                    
                FileUpload::make('gambar')
                    ->label('Gambar/Banner Wisata')
                    ->image()
                    ->directory('wisata-images')
                    ->maxSize(5120) // 5MB
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function handleRegistration(array $data): Model
    {
        // 1. Create User dengan role pengelola
        $user = $this->getUserModel()::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'pengelola',
        ]);
        
        // 2. Create Wisata dengan status pending
        Wisata::create([
            'nama_wisata' => $data['nama_wisata'],
            'deskripsi' => $data['deskripsi'],
            'lokasi' => $data['lokasi'],
            'harga_tiket' => $data['harga_tiket'],
            'id_pengelola' => $user->id,
            'id_wilayah' => $data['id_wilayah'],
            'gambar' => $data['gambar'] ?? null,
            'status' => 'pending', // Belum bisa diedit sampai disetujui
        ]);
        
        return $user;
    }
}
