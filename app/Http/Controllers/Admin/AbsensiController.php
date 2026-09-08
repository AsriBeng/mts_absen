<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKey;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Kunci Barcode Aktif
        $activeKey = AbsensiKey::where('is_active', true)->first();

        // 2. Tangkap Tanggal dari Filter (Default: Hari Ini)
        $selectedDate = $request->get('date', now()->toDateString());

        // 3. Ambil Data Absensi Berdasarkan Tanggal yang Dipilih
        $attendances = Absensi::with('user')
            ->whereDate('date', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('app.admin.absensi', compact('activeKey', 'attendances', 'selectedDate'));
    }

    // Method Generate Kunci Barcode Baru
    public function generateKey(Request $request)
    {
        AbsensiKey::query()->update(['is_active' => false]);

        $newKey = 'ALHUDA-' . rand(1000, 9999) . '-' . strtoupper(Str::random(6));

        AbsensiKey::create([
            'key_code'  => $newKey,
            'name'      => 'Barcode Utama Kantor',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Barcode Absensi berhasil diperbarui!');
    }
}
