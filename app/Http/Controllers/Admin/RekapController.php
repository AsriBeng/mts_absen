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
        $data = $this->getRekapData($request);
        return view('app.admin.rekap', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getRekapData($request);
        return view('app.admin.rekap_pdf', $data);
    }

    private function getRekapData(Request $request)
    {
        // Tangkap Filter (Default: harian)
        $filterType = $request->get('filter_type', 'harian');
        $selectedDate = $request->get('date', now()->toDateString());
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        $query = Absensi::with('user');

        // Logika Filter Periode
        if ($filterType === 'harian') {
            $query->whereDate('date', $selectedDate);
            $periodeText = Carbon::parse($selectedDate)->translatedFormat('d F Y');

        } elseif ($filterType === 'mingguan') {
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

        // Ringkasan Statistik Data Rekap
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
