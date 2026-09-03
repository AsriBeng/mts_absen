@extends('layouts.layout')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Utama')

@section('content')
<div class="space-y-6">

    {{-- Widget Hari & Jam Digital Real-time --}}
    <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl p-6 text-white shadow-sm relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-emerald-200">
                    {{ date('l, d F Y') }}
                </p>
                <h3 class="text-3xl font-extrabold mt-1 tracking-wider">
                    <span id="realtime-clock">{{ date('H:i:s') }}</span>
                    <span class="text-sm font-normal">WIB</span>
                </h3>
            </div>
            <div class="text-xs bg-emerald-900/40 backdrop-blur-md px-4 py-2 rounded-xl border border-emerald-400/30">
                <i class="fas fa-info-circle mr-1 text-emerald-300"></i>
                Status Hari Ini: <span class="font-bold text-emerald-200">Jam Kerja Normal</span>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
    </div>

    {{-- Card Ringkasan Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Hadir Tepat Waktu</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">12</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Terlambat</p>
                <h3 class="text-2xl font-bold text-amber-500 mt-1">3</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Izin / Sakit</p>
                <h3 class="text-2xl font-bold text-blue-500 mt-1">1</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
                <i class="fas fa-envelope-open-text"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tanpa Keterangan</p>
                <h3 class="text-2xl font-bold text-rose-500 mt-1">0</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

    {{-- Tabel Presensi Hari Ini --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Presensi Guru Hari Ini</h3>
            <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium">{{ date('d M Y') }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Nama Guru</th>
                        <th class="px-6 py-3 font-semibold">Jam Masuk</th>
                        <th class="px-6 py-3 font-semibold">Jam Pulang</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold text-center">Selfie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">Guru Al-Huda</td>
                        <td class="px-6 py-4">07:15:20</td>
                        <td class="px-6 py-4 text-slate-400">-</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Hadir</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="text-xs text-blue-600 hover:underline">Lihat Foto</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Script Jam Realtime --}}
<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const clockElement = document.getElementById('realtime-clock');
        if (clockElement) {
            clockElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
