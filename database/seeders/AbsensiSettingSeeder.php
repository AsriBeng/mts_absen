<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AbsensiSetting;

class AbsensiSettingSeeder extends Seeder
{
public function run(): void
    {
        $days = [
            'Senin'  => 'masuk',
            'Selasa' => 'masuk',
            'Rabu'   => 'masuk',
            'Kamis'  => 'masuk',
            'Jumat'  => 'masuk',
            'Sabtu'  => 'masuk',
            'Minggu' => 'libur',
        ];

        foreach ($days as $day => $status) {
            AbsensiSetting::updateOrCreate(
                ['day_name' => $day],
                [
                    'time_in'                => '00:00:00',
                    'time_out'               => '00:00:00',
                    'late_tolerance_minutes' => 0,
                    'office_latitude'        => 0,
                    'office_longitude'       => 0,
                    'radius_meters'          => 0,
                    'status'                 => $status,
                ]
            );
        }
    }
}
