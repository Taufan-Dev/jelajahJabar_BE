<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wilayah;

class WilayahPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'super_admin') {
            return true;
        }
        return false; // Only super_admin can do anything with wilayah
    }
}
