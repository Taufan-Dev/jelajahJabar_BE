<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $guarded = [];

    public function admins()
    {
        return $this->hasMany(User::class, 'id_wilayah');
    }

    public function wisatas()
    {
        return $this->hasMany(Wisata::class, 'id_wilayah');
    }
}
