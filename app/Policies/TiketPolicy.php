<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tiket;

class TiketPolicy
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

    public function view(User $user, Tiket $tiket): bool
    {
        $tiket->loadMissing('wisata');
        if ($user->role === 'pengelola') {
            return $tiket->wisata->id_pengelola === $user->id;
        }
        return $user->role === 'admin_wilayah' && $tiket->wisata->id_wilayah === $user->id_wilayah;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Tiket $tiket): bool
    {
        return false;
    }

    public function delete(User $user, Tiket $tiket): bool
    {
        return false;
    }
}
