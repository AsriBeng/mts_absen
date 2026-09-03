<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSetting extends Model
{
    // Nama tabel di database (opsional tapi disarankan agar eksplisit)
    protected $table = 'absensi_settings';

    // Kolom yang dapat diisi secara otomatis (mass assignable)
    protected $fillable = [
        'day_name',
        'time_in',
        'time_out',
        'late_tolerance_minutes',
        'office_latitude',
        'office_longitude',
        'radius_meters',
        'status',
    ];
}
