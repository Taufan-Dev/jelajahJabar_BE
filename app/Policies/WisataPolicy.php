<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wisata;

class WisataPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'super_admin') {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin_wilayah', 'pengelola']);
    }

    public function view(User $user, Wisata $wisata): bool
    {
        if ($user->role === 'pengelola') {
            return $user->id === $wisata->id_pengelola;
        }
        return $user->role === 'admin_wilayah' && $user->id_wilayah === $wisata->id_wilayah;
    }

    public function create(User $user): bool
    {
        return false; // Dibuat saat registrasi
    }

    public function update(User $user, Wisata $wisata): bool
    {
        // Pengelola hanya bisa mengedit data wisatanya jika sudah disetujui penuh
        if ($user->role === 'pengelola') {
            return $user->id === $wisata->id_pengelola && $wisata->status === 'disetujui_super_admin';
        }
        return $user->role === 'admin_wilayah' && $user->id_wilayah === $wisata->id_wilayah;
    }

    public function delete(User $user, Wisata $wisata): bool
    {
        return false;
    }
}
