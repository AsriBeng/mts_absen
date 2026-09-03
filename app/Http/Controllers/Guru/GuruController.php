<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GuruController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // Ambil data presensi hari ini
        $todayAttendance = Absensi::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        // Ambil 3 riwayat presensi terakhir
        $recentAttendances = Absensi::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        return view('app.guru.dashboard', compact('todayAttendance', 'recentAttendances'));
    }
}
