<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'user_id',
        'absensi_key_id',
        'date',
        'time_in',
        'time_out',
        'image_in',
        'image_out',
        'user_latitude',
        'user_longitude',
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke AbsensiKey
    public function absensiKey()
    {
        return $this->belongsTo(AbsensiKey::class);
    }
}
