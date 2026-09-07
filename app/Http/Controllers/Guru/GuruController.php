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
        // Validasi input
        $request->validate([
            'qr_code'   => 'required',
            'image'     => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
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
                'message' => 'QR Code tidak valid atau sudah tidak aktif!'
            ]);
        }

        // 2. Ambil Setting Hari Ini
        $dayNameMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $currentDayName = $dayNameMap[date('l')];
        $setting = AbsensiSetting::where('day_name', $currentDayName)->first();

        if (!$setting || $setting->status === 'libur') {
            return response()->json([
                'success' => false,
                'message' => 'Hari ini adalah hari libur!'
            ]);
        }

        // 3. Simpan Gambar Selfie
        $image = $request->image;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'selfie_' . Str::random(10) . '_' . time() . '.png';
        Storage::disk('public')->put('absensi/' . $imageName, base64_decode($image));
        $filePath = 'absensi/' . $imageName;

        // 4. Cari Data Presensi Hari Ini
        $attendance = Absensi::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        // --- PROSES ABSEN MASUK ---
        if (!$attendance) {
            $timeInOfficial = Carbon::parse($setting->time_in);
            $timeInLimit = $timeInOfficial->copy()->addMinutes($setting->late_tolerance_minutes);

            // Jika scan melewati batas toleransi -> Terlambat
            $status = $nowTime->gt($timeInLimit) ? 'terlambat' : 'hadir';

            Absensi::create([
                'user_id'        => $userId,
                'absensi_key_id' => $activeKey->id,
                'date'           => $today,
                'time_in'        => $nowTime->format('H:i:s'),
                'status'         => $status,
                'image_in'       => $filePath,
                'user_latitude'  => $request->latitude,
                'user_longitude' => $request->longitude,
                'keterangan'     => '-',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil tercatat!'
            ]);
        }

        // --- PROSES ABSEN PULANG ---
        else {
            if ($attendance->time_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini!'
                ]);
            }

            $timeOutOfficial = Carbon::parse($setting->time_out);

            // Tolak jika belum jam pulang
            if ($nowTime->lt($timeOutOfficial)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum waktunya pulang! Jam pulang hari ini adalah ' . $timeOutOfficial->format('H:i') . ' WIB.'
                ]);
            }

            $attendance->update([
                'time_out'  => $nowTime->format('H:i:s'),
                'image_out' => $filePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen pulang berhasil tercatat!'
            ]);
        }
    }
}
