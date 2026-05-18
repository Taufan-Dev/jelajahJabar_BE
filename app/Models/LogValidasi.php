<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogValidasi extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_validasi' => 'datetime',
        ];
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
