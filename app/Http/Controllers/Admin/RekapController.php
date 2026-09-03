<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap Parameter Filter (Default: Harian & Hari Ini)
        $filterType    = $request->get('filter_type', 'harian');
        $selectedDate  = $request->get('date', now()->toDateString());
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear  = $request->get('year', date('Y'));

        // 2. Query Utama dengan Eager Loading Relasi User/Guru
        $query = Absensi::with('user');

        // 3. Logika Filter Berdasarkan Periode
        if ($filterType === 'harian') {
            $query->whereDate('date', $selectedDate);
            $periodeText = Carbon::parse($selectedDate)->translatedFormat('d F Y');
        } elseif ($filterType === 'mingguan') {
            $startDate = Carbon::parse($selectedDate);
            $endDate   = $startDate->copy()->addDays(6);

            $query->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
            $periodeText = $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y') . ' (7 Hari)';
        } elseif ($filterType === 'tahunan') {
            $query->whereYear('date', $selectedYear);
            $periodeText = 'Tahun ' . $selectedYear;
        } else { // Bulanan
            $query->whereMonth('date', $selectedMonth)
                ->whereYear('date', $selectedYear);
            $periodeText = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
        }

        // Ambil Data Absensi Berdasarkan Urutan Tanggal Terbaru
        $attendances = $query->orderBy('date', 'desc')->latest()->get();

        // 4. Hitung Statistik Ringkasan Seluruh Guru
        $stats = [
            'total_hadir'     => $attendances->where('status', 'hadir')->count(),
            'total_terlambat' => $attendances->where('status', 'terlambat')->count(),
            'total_izin'      => $attendances->where('status', 'izin')->count(),
            'total_alpa'      => $attendances->where('status', 'alpa')->count(),
        ];

        // 5. Kirim Semua Variabel ke View
        return view('app.admin.rekap', compact(
            'attendances',
            'filterType',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'periodeText',
            'stats'
        ));
    }
}
