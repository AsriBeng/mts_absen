<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AbsensiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        return view('app.guru.absensaya_pdf', array_merge($data, ['user' => $user]));
    }

    /**
     * Memproses Scan QR, Foto Selfie, dan Koordinat GPS
     */
    public function storeScan(Request $request)
    {
        // 1. Validasi Input Form (QR, Selfie, dan Titik GPS)
        $request->validate([
            'qr_code'   => 'required|string',
            'image'     => 'required|string',
            'latitude'  => 'required|string',
            'longitude' => 'required|string',
        ], [
            'qr_code.required'   => 'Kode QR wajib di-scan!',
            'image.required'     => 'Foto selfie wajib diambil!',
            'latitude.required'  => 'Titik lokasi GPS wajib diaktifkan!',
            'longitude.required' => 'Titik lokasi GPS wajib diaktifkan!',
        ]);

        // 2. Validasi Kunci QR Code
        $validKey = AbsensiKey::where('key_code', $request->qr_code)
            ->where('is_active', true)
            ->first();

        if (!$validKey) {
            return redirect()->back()->with('error', 'Kode QR tidak valid atau sudah kadaluarsa!');
        }

        $userId      = Auth::id();
        $today       = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // 3. Simpan Gambar Selfie (Base64 Decode)
        $imageName = null;
        if ($request->image) {
            $imageParts = explode(";base64,", $request->image);
            $imageTypeAux = explode("image/", $imageParts[0]);
            $imageType = $imageTypeAux[1] ?? 'png';
            $imageBase64 = base64_decode($imageParts[1] ?? $request->image);

            $imageName = 'selfie_' . $userId . '_' . time() . '.' . $imageType;
            Storage::disk('public')->put('absensi/' . $imageName, $imageBase64);
        }

        // 4. Pengecekan Presensi Hari Ini (Masuk atau Pulang)
        $absensiHariIni = Absensi::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$absensiHariIni) {
            // Logika Jam Masuk (Contoh batas waktu jam 07:00:00)
            $jamBatasHadir = Carbon::createFromTimeString('07:00:00');
            $status = Carbon::now()->greaterThan($jamBatasHadir) ? 'terlambat' : 'hadir';

            Absensi::create([
                'user_id'      => $userId,
                'date'         => $today,
                'time_in'      => $currentTime,
                'status'       => $status,
                'image_in'     => $imageName ? 'absensi/' . $imageName : null,
                'lat_in'       => $request->latitude,
                'long_in'      => $request->longitude,
            ]);

            return redirect()->back()->with('success', 'Berhasil melakukan presensi masuk!');
        } else {
            // Cek jika sudah pernah absen masuk & absen pulang
            if ($absensiHariIni->time_out) {
                return redirect()->back()->with('error', 'Anda sudah melakukan presensi masuk dan pulang hari ini.');
            }

            // Update Jam Pulang beserta Selfie Pulang & Lokasi Pulang
            $absensiHariIni->update([
                'time_out'  => $currentTime,
                'image_out' => $imageName ? 'absensi/' . $imageName : null,
                'lat_out'   => $request->latitude,
                'long_out'  => $request->longitude,
            ]);

            return redirect()->back()->with('success', 'Berhasil melakukan presensi pulang!');
        }
    }

    private function getAbsensiData(Request $request)
    {
        $filterType = $request->get('filter_type', 'mingguan');
        $selectedDate = $request->get('date', now()->toDateString());
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        $query = Absensi::where('user_id', Auth::id());

        if ($filterType === 'mingguan') {
            $startDate = Carbon::parse($selectedDate);
            $endDate = $startDate->copy()->addDays(6);

            $query->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);
            $periodeText = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y') . ' (7 Hari)';
        } elseif ($filterType === 'tahunan') {
            $query->whereYear('date', $selectedYear);
            $periodeText = 'Tahun ' . $selectedYear;
        } else {
            $query->whereMonth('date', $selectedMonth)
                ->whereYear('date', $selectedYear);
            $periodeText = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
        }

        $attendances = $query->orderBy('date', 'desc')->get();

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
