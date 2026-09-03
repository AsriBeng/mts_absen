<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSetting;
use Illuminate\Http\Request;

class DaySettingController extends Controller
{
    public function index()
    {
        // Ambil semua data hari
        $settings = AbsensiSetting::orderBy('id', 'asc')->get();

        // Hitung total data yang ada di database
        $totalCount = $settings->count();

        return view('app.admin.days', compact('settings', 'totalCount'));
    }

    public function store(Request $request)
    {
        // Batas maksimal 7 data
        if (AbsensiSetting::count() >= 7) {
            return redirect()->back()->with('error', 'Gagal menambahkan data! Maksimal pengaturan hari adalah 7 data.');
        }

        $request->validate([
            'day_name'               => 'required|string|max:255',
            'time_in'                => 'required',
            'time_out'               => 'required',
            'late_tolerance_minutes' => 'required|numeric|min:0',
            'office_latitude'        => 'required|numeric',
            'office_longitude'       => 'required|numeric',
            'radius_meters'          => 'required|numeric|min:1',
            'status'                 => 'required|in:masuk,libur',
        ]);

        AbsensiSetting::create([
            'day_name'               => $request->day_name,
            'time_in'                => $request->time_in,
            'time_out'               => $request->time_out,
            'late_tolerance_minutes' => $request->late_tolerance_minutes,
            'office_latitude'        => $request->office_latitude,
            'office_longitude'       => $request->office_longitude,
            'radius_meters'          => $request->radius_meters,
            'status'                 => $request->status,
        ]);

        return redirect()->back()->with('success', 'Pengaturan hari berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $setting = AbsensiSetting::findOrFail($id);

        $request->validate([
            'day_name'               => 'required|string|max:255',
            'time_in'                => 'required',
            'time_out'               => 'required',
            'late_tolerance_minutes' => 'required|numeric|min:0',
            'office_latitude'        => 'required|numeric',
            'office_longitude'       => 'required|numeric',
            'radius_meters'          => 'required|numeric|min:1',
            'status'                 => 'required|in:masuk,libur',
        ]);

        $setting->update([
            'day_name'               => $request->day_name,
            'time_in'                => $request->time_in,
            'time_out'               => $request->time_out,
            'late_tolerance_minutes' => $request->late_tolerance_minutes,
            'office_latitude'        => $request->office_latitude,
            'office_longitude'       => $request->office_longitude,
            'radius_meters'          => $request->radius_meters,
            'status'                 => $request->status,
        ]);

        return redirect()->back()->with('success', 'Pengaturan hari berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $setting = AbsensiSetting::findOrFail($id);
        $setting->delete();

        return redirect()->back()->with('success', 'Data pengaturan hari berhasil dihapus!');
    }
}
