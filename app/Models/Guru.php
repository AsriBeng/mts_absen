<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'nik',
        'nama_lengkap',
        'email', // <-- Tambahkan ke $fillable
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'no_hp',
        'status_kepegawaian',
        'jabatan',
        'foto_profile',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
