<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiKey extends Model
{
    protected $fillable = [
        'key_code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
