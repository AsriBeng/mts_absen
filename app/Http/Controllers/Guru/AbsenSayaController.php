<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsenSayaController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getAbsensiData($request);
        return view('app.guru.absensaya', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getAbsensiData($request);
        $user = Auth::user();

        // Mengirim data ke view khusus cetak PDF
        return view('app.guru.absensaya_pdf', array_merge($data, ['user' => $user]));
    }

    private function getAbsensiData(Request $request)
    {
        // Tangkap Filter (Default: Mingguan)
        $filterType = $request->get('filter_type', 'mingguan');
        $selectedDate = $request->get('date', now()->toDateString());
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Query Absensi Hanya Untuk Guru yang Sedang Login
        $query = Absensi::where('user_id', Auth::id());

        // Logika Filter Periode
        if ($filterType === 'mingguan') {
            $startDate = Carbon::parse($selectedDate);
            $endDate = $startDate->copy()->addDays(6);

            $query->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
            $periodeText = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y') . ' (7 Hari)';

        } elseif ($filterType === 'tahunan') {
            $query->whereYear('date', $selectedYear);
            $periodeText = 'Tahun ' . $selectedYear;

        } else { // Bulanan
            $query->whereMonth('date', $selectedMonth)
                  ->whereYear('date', $selectedYear);
            $periodeText = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        // Ringkasan Statistik Presensi Diri Sendiri
        $stats = [
            'total_hadir'     => $attendances->where('status', 'hadir')->count(),
            'total_terlambat' => $attendances->where('status', 'terlambat')->count(),
            'total_izin'      => $attendances->where('status', 'izin')->count(),
            'total_alpa'      => $attendances->where('status', 'alpa')->count(),
        ];

        return compact(
            'attendances',
            'filterType',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'periodeText',
            'stats'
        );
    }
}
