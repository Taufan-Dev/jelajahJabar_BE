<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_kunjungan' => 'date',
            'tanggal_digunakan' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wisata()
    {
        return $this->belongsTo(Wisata::class);
    }

    public function logValidasis()
    {
        return $this->hasMany(LogValidasi::class);
    }
}
