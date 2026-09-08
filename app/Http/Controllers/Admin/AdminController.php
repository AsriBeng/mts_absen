<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AbsensiSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Ambil aturan hari ini dari absensi_settings
        $todayDayName = Carbon::now()->locale('id')->isoFormat('dddd');
        $todaySetting = AbsensiSetting::where('day_name', $todayDayName)->first();

        // Hitung Ringkasan Status
        $hadirTepatWaktu = Absensi::whereDate('created_at', $today)
            ->where('status', 'Hadir')
            ->count();

        $terlambat = Absensi::whereDate('created_at', $today)
            ->where('status', 'Terlambat')
            ->count();

        $izinSakit = Absensi::whereDate('created_at', $today)
            ->whereIn('status', ['Izin', 'Sakit'])
            ->count();

        $tanpaKeterangan = Absensi::whereDate('created_at', $today)
            ->where('status', 'Alpha')
            ->count();

        // Ambil Daftar Presensi Hari Ini
        $presensiHariIni = Absensi::with('user')
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        return view('app.admin.dashboard', compact(
            'todaySetting',
            'hadirTepatWaktu',
            'terlambat',
            'izinSakit',
            'tanpaKeterangan',
            'presensiHariIni'
        ));
    }
}
