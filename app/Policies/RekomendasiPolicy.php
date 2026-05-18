<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rekomendasi;

class RekomendasiPolicy
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

    public function view(User $user, Rekomendasi $rekomendasi): bool
    {
        $rekomendasi->loadMissing('wisata');
        if ($user->role === 'pengelola') {
            return $rekomendasi->wisata->id_pengelola === $user->id;
        }
        return $user->role === 'admin_wilayah' && $rekomendasi->wisata->id_wilayah === $user->id_wilayah;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Rekomendasi $rekomendasi): bool
    {
        return false;
    }

    public function delete(User $user, Rekomendasi $rekomendasi): bool
    {
        return false;
    }
}
