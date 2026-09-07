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
                'title'   => 'QR Code Tidak Valid',
                'message' => 'QR Code tidak valid atau sudah tidak aktif!'
            ]);
        }

        // 2. Ambil Setting Hari Ini (Cek Bahasa Indonesia & Inggris)
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

        // 3. Cari Data Presensi Hari Ini
        $attendance = Absensi::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        // ====================================================
        // --- 1. PROSES ABSEN MASUK ---
        // ====================================================
        if (!$attendance) {
            $timeInOfficial = Carbon::parse($setting->time_in);

            // Batas Awal Buka Absen Masuk (3 jam sebelum time_in)
            $timeInOpenLimit = $timeInOfficial->copy()->subHours(3);

            // PENGECEKAN: Jika scan dilakukan sebelum jam buka (misal: < 04:00)
            if ($nowTime->lt($timeInOpenLimit)) {
                return response()->json([
                    'success' => false,
                    'title'   => 'Absen Belum Dibuka',
                    'message' => 'Absen masuk belum dibuka! Absen masuk baru dibuka mulai pukul ' . $timeInOpenLimit->format('H:i') . ' WIB.'
                ]);
            }

            // Simpan Foto Selfie untuk Absen Masuk
            $image = $request->image;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'selfie_in_' . Str::random(10) . '_' . time() . '.png';
            Storage::disk('public')->put('absensi/' . $imageName, base64_decode($image));
            $filePath = 'absensi/' . $imageName;

            // Batas Toleransi Terlambat
            $timeInLateLimit = $timeInOfficial->copy()->addMinutes($setting->late_tolerance_minutes);

            // Menentukan status kehadiran
            $status = $nowTime->gt($timeInLateLimit) ? 'terlambat' : 'hadir';

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

            $statusText = ($status === 'terlambat') ? ' (Status: Terlambat)' : ' (Status: Tepat Waktu)';

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil tercatat!' . $statusText
            ]);
        }

        // ====================================================
        // --- 2. PROSES ABSEN PULANG ---
        // ====================================================
        else {
            if ($attendance->time_out) {
                return response()->json([
                    'success' => false,
                    'title'   => 'Sudah Absen Pulang',
                    'message' => 'Anda sudah melakukan absen pulang hari ini!'
                ]);
            }

            $timeOutOfficial = Carbon::parse($setting->time_out);

            // Batas Akhir Tutup Absen Pulang (3 jam setelah time_out)
            $timeOutCloseLimit = $timeOutOfficial->copy()->addHours(3);

            // PENGECEKAN 1: Jika belum waktunya pulang (jam scan < time_out)
            if ($nowTime->lt($timeOutOfficial)) {
                return response()->json([
                    'success' => false,
                    'title'   => 'Belum Waktunya Pulang',
                    'message' => 'Belum waktunya pulang! Jam pulang hari ini adalah pukul ' . $timeOutOfficial->format('H:i') . ' WIB.'
                ]);
            }

            // PENGECEKAN 2: Jika sudah melewati batas 4 jam setelah time_out (misal: > 18:00 jika time_out 14:00)
            if ($nowTime->gt($timeOutCloseLimit)) {
                return response()->json([
                    'success' => false,
                    'title'   => 'Absen Telah Ditutup',
                    'message' => 'Absen pulang telah ditutup! Batas waktu maksimal absen pulang adalah pukul ' . $timeOutCloseLimit->format('H:i') . ' WIB.'
                ]);
            }

            // Update Jam Pulang (Tanpa menyimpan foto untuk menghemat storage)
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
