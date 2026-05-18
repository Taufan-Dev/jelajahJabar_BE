<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wisata extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::updated(function ($wisata) {
            if ($wisata->isDirty('status')) {
                $user = auth()->user();
                $actorName = $user ? $user->name : 'Sistem';
                $newStatus = $wisata->status;

                $pengelola = $wisata->pengelola; // Relasi ke User pengelola
                $wilayahAdmins = \App\Models\User::where('role', 'admin_wilayah')
                    ->where('id_wilayah', $wisata->id_wilayah)
                    ->get();
                $superAdmins = \App\Models\User::where('role', 'super_admin')->get();

                if ($newStatus === 'disetujui_admin') {
                    // 1. Kirim notifikasi ke Pengelola Wisata
                    if ($pengelola) {
                        \Filament\Notifications\Notification::make()
                            ->title('Wisata Disetujui Admin Wilayah')
                            ->body("Wisata \"{$wisata->nama_wisata}\" telah disetujui oleh Admin Wilayah ({$actorName}) dan sedang menunggu persetujuan akhir dari Super Admin.")
                            ->success()
                            ->sendToDatabase($pengelola);
                    }

                    // 2. Kirim notifikasi ke semua Super Admin
                    \Filament\Notifications\Notification::make()
                        ->title('Menunggu Persetujuan Akhir')
                        ->body("Wisata \"{$wisata->nama_wisata}\" telah disetujui oleh Admin Wilayah ({$actorName}) dan kini memerlukan persetujuan akhir Anda.")
                        ->info()
                        ->sendToDatabase($superAdmins);
                        
                } elseif ($newStatus === 'disetujui_super_admin') {
                    // 1. Kirim notifikasi ke Pengelola Wisata
                    if ($pengelola) {
                        \Filament\Notifications\Notification::make()
                            ->title('Selamat! Wisata Aktif Sepenuhnya')
                            ->body("Wisata \"{$wisata->nama_wisata}\" telah disetujui sepenuhnya oleh Super Admin ({$actorName}) dan kini sudah tampil di aplikasi!")
                            ->success()
                            ->sendToDatabase($pengelola);
                    }

                    // 2. Kirim notifikasi ke semua Admin Wilayah di wilayah tersebut
                    \Filament\Notifications\Notification::make()
                        ->title('Wisata Aktif Sepenuhnya')
                        ->body("Wisata \"{$wisata->nama_wisata}\" di wilayah Anda telah disetujui sepenuhnya oleh Super Admin ({$actorName}).")
                        ->success()
                        ->sendToDatabase($wilayahAdmins);
                }
            }
        });
    }

    public function pengelola()
    {
        return $this->belongsTo(User::class, 'id_pengelola');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'id_wilayah');
    }

    public function tikets()
    {
        return $this->hasMany(Tiket::class, 'wisata_id');
    }

    public function rekomendasis()
    {
        return $this->hasMany(Rekomendasi::class, 'wisata_id');
    }
}
