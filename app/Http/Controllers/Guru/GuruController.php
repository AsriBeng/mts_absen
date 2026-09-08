<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AbsensiKey;
use App\Models\AbsensiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuruController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // 1. Ambil nama hari dalam Bahasa Indonesia (Senin, Selasa, dst.)
        $todayDayName = Carbon::now()->locale('id')->isoFormat('dddd');

        // 2. Ambil aturan jam kerja hari ini dari absensi_settings
        $todaySetting = AbsensiSetting::where('day_name', $todayDayName)->first();

        // 3. Ambil data presensi guru hari ini
        $todayAttendance = Absensi::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        // 4. Ambil 5 riwayat presensi terakhir
        $recentAttendances = Absensi::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return view('app.guru.dashboard', compact('todayAttendance', 'todaySetting', 'recentAttendances'));
    }

    public function storeScan(Request $request)
        {
            $request->validate([
                'qr_code'   => 'required',
                'image'     => 'required',
                'latitude'  => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $userId = Auth::id();
            $today = date('Y-m-d');
            $nowTime = Carbon::now('Asia/Jakarta');

            // 1. Cek Kunci QR Code
            $activeKey = AbsensiKey::where('key_code', $request->qr_code)
                ->where('is_active', true)
                ->first();

            if (!$activeKey) {
                return response()->json([
                    'success' => false,
                    'title'   => 'QR Code Tidak Valid',
                    'message' => 'QR Code tidak valid atau sudah tidak aktif!'
                ]);
            }

            // 2. Ambil Setting Hari Ini
            $dayNameMap = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            $englishDay = date('l');
            $indonesianDay = $dayNameMap[$englishDay];

            $setting = AbsensiSetting::whereIn('day_name', [$englishDay, $indonesianDay])->first();

            if (!$setting || $setting->status === 'libur') {
                return response()->json([
                    'success' => false,
                    'title'   => 'Hari Libur',
                    'message' => 'Hari ini adalah hari libur!'
                ]);
            }

            // --- VALIDASI GEOLOKASI GPS RADIUS (GEOFENCING) ---
            $userLat = $request->latitude;
            $userLng = $request->longitude;
            $officeLat = $setting->office_latitude;
            $officeLng = $setting->office_longitude;

            $distance = $this->calculateDistance($userLat, $userLng, $officeLat, $officeLng);

            if ($distance > $setting->radius_meters) {
                return response()->json([
                    'success' => false,
                    'title'   => 'Luar Area Sekolah',
                    'message' => 'Absen ditolak! Anda tidak berada dalam area sekolah.'
                ]);
            }

            // 3. Cari Data Presensi Hari Ini
            $attendance = Absensi::where('user_id', $userId)
                ->where('date', $today)
                ->first();

            // --- PROSES ABSEN MASUK ---
            if (!$attendance) {
                // Cek Batas Waktu Buka Absen Masuk (3 Jam sebelum time_in)
                $timeInOfficial = Carbon::parse($setting->time_in);
                $timeInStart = $timeInOfficial->copy()->subHours(3);

                if ($nowTime->lt($timeInStart)) {
                    return response()->json([
                        'success' => false,
                        'title'   => 'Absen Belum Dibuka',
                        'message' => 'Absen masuk belum dibuka. Absen dibuka mulai jam ' . $timeInStart->format('H:i') . ' WIB.'
                    ]);
                }

                // Simpan foto selfie masuk
                $image = $request->image;
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'selfie_in_' . Str::random(10) . '_' . time() . '.png';
                Storage::disk('public')->put('absensi/' . $imageName, base64_decode($image));
                $filePath = 'absensi/' . $imageName;

                $timeInLimit = $timeInOfficial->copy()->addMinutes($setting->late_tolerance_minutes);
                $status = $nowTime->gt($timeInLimit) ? 'terlambat' : 'hadir';

                Absensi::create([
                    'user_id'        => $userId,
                    'absensi_key_id' => $activeKey->id,
                    'date'           => $today,
                    'time_in'        => $nowTime->format('H:i:s'),
                    'status'         => $status,
                    'image_in'       => $filePath,
                    'user_latitude'  => $userLat,
                    'user_longitude' => $userLng,
                    'keterangan'     => '-',
                ]);

                $statusText = ($status === 'terlambat') ? ' (Status: Terlambat)' : ' (Status: Tepat Waktu)';

                return response()->json([
                    'success' => true,
                    'message' => 'Absen masuk berhasil tercatat!' . $statusText
                ]);
            }

            // --- PROSES ABSEN PULANG ---
            else {
                if ($attendance->time_out) {
                    return response()->json([
                        'success' => false,
                        'title'   => 'Sudah Absen Pulang',
                        'message' => 'Anda sudah melakukan absen pulang hari ini!'
                    ]);
                }

                $timeOutOfficial = Carbon::parse($setting->time_out);
                $timeOutEnd = $timeOutOfficial->copy()->addHours(4);

                if ($nowTime->lt($timeOutOfficial)) {
                    return response()->json([
                        'success' => false,
                        'title'   => 'Belum Waktunya Pulang',
                        'message' => 'Belum waktunya pulang! Jam pulang hari ini adalah ' . $timeOutOfficial->format('H:i') . ' WIB.'
                    ]);
                }

                if ($nowTime->gt($timeOutEnd)) {
                    return response()->json([
                        'success' => false,
                        'title'   => 'Absen Telah Ditutup',
                        'message' => 'Absen pulang telah ditutup. Batas waktu absen pulang adalah jam ' . $timeOutEnd->format('H:i') . ' WIB.'
                    ]);
                }

                $attendance->update([
                    'time_out'  => $nowTime->format('H:i:s'),
                    'image_out' => null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Absen pulang berhasil tercatat!'
                ]);
            }
        }

    /**
     * Hitung jarak dua titik koordinat GPS (dalam meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
    /**
     * Tampilan Halaman Form Izin
     */
    public function izinForm()
    {
        $userId = Auth::id();
        $today = date('Y-m-d');

        // Cek jika sudah presensi/izin hari ini
        $alreadyAbsence = Absensi::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($alreadyAbsence) {
            return redirect()->route('guru.dashboard')->with('error', 'Anda sudah melakukan presensi/izin hari ini!');
        }

        return view('app.guru.izin');
    }

    /**
     * Simpan Data Izin Guru (Bukti disimpan ke image_in)
     */
    public function storeIzin(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string',
            'bukti'      => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $userId = Auth::id();
        $today = date('Y-m-d');

        // Cek kembali ketersediaan data hari ini
        $alreadyAbsence = Absensi::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($alreadyAbsence) {
            return redirect()->route('guru.dashboard')->with('error', 'Anda sudah melakukan presensi/izin hari ini!');
        }

        // Upload Gambar Bukti ke Storage (Storage disk public)
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = 'bukti_izin_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('absensi', $fileName, 'public');
        }

        // Simpan ke database dengan status 'izin' dan bukti di 'image_in'
        Absensi::create([
            'user_id'    => $userId,
            'date'       => $today,
            'time_in'    => Carbon::now('Asia/Jakarta')->format('H:i:s'),
            'status'     => 'izin',
            'image_in'   => $filePath ?? null, // Foto bukti diletakkan di image_in
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('guru.dashboard')->with('success', 'Pengajuan izin berhasil disimpan!');
    }
}
