<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
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
        return $user->role === 'admin_wilayah';
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin_wilayah' && $model->role === 'pengelola' && $model->id_wilayah === $user->id_wilayah;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin_wilayah';
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin_wilayah' && $model->role === 'pengelola' && $model->id_wilayah === $user->id_wilayah;
    }

    public function delete(User $user, User $model): bool
    {
        return false; // Disable hapus untuk admin_wilayah agar relasi aman
    }
}
