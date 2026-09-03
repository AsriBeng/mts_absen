<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi; // Ubah dengan nama Model Presensi Anda jika berbeda
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Hitung Ringkasan Status Hari Ini
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
            ->where('status', 'Alpha') // Sesuaikan value status di DB
            ->count();

        // 2. Ambil Daftar Presensi Hari Ini beserta Data Guru
        $presensiHariIni = Absensi::with('user') // Ubah 'user' sesuai nama relasi di Model Absensi
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        // 3. Kirim Variabel ke View
        return view('app.admin.dashboard', compact(
            'hadirTepatWaktu',
            'terlambat',
            'izinSakit',
            'tanpaKeterangan',
            'presensiHariIni'
        ));
    }
}
