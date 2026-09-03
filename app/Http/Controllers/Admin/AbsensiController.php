<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKey;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    public function index()
    {
        // Ambil data kunci barcode kantor yang aktif (jika belum ada, ambil null)
        $activeKey = AbsensiKey::where('is_active', true)->first();

        // Ambil data riwayat absensi hari ini
        $attendances = Absensi::with('user')
            ->whereDate('date', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('app.admin.absensi', compact('activeKey', 'attendances'));
    }

    // Method untuk Generate Kunci Barcode Baru
    public function generateKey(Request $request)
    {
        // Nonaktifkan kunci lama
        AbsensiKey::query()->update(['is_active' => false]);

        // Buat kunci unik baru
        $newKey = 'ALHUDA-' . rand(1000, 9999) . '-' . strtoupper(Str::random(8));

        AbsensiKey::create([
            'key_code'  => $newKey,
            'name'      => 'Barcode Utama Kantor',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Barcode Absensi berhasil diperbarui!');
    }
}
