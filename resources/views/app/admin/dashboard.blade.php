@extends('layouts.layout')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Utama')

@section('content')
    <div class="space-y-6">

        {{-- Widget Hari & Jam Digital Real-time --}}
        <div
            class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl p-6 text-white shadow-sm relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
                <div>
                    <p class="text-xs uppercase tracking-wider font-semibold text-emerald-200">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
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

        {{-- Card Ringkasan Status Dinamis --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Hadir Tepat Waktu</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $hadirTepatWaktu }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Terlambat</p>
                    <h3 class="text-2xl font-bold text-amber-500 mt-1">{{ $terlambat }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Izin / Sakit</p>
                    <h3 class="text-2xl font-bold text-blue-500 mt-1">{{ $izinSakit }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tanpa Keterangan</p>
                    <h3 class="text-2xl font-bold text-rose-500 mt-1">{{ $tanpaKeterangan }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

        {{-- Tabel Presensi Hari Ini Dinamis --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Presensi Guru Hari Ini</h3>
                <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium">
                    {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                </span>
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
                        @forelse ($presensiHariIni as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $row->user->name ?? 'Guru Noname' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($row->status == 'Hadir')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Hadir</span>
                                    @elseif($row->status == 'Terlambat')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">Terlambat</span>
                                    @elseif(in_array($row->status, ['Izin', 'Sakit']))
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600">{{ $row->status }}</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600">{{ $row->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($row->image_path)
                                        <a href="{{ asset('storage/' . $row->image_path) }}" target="_blank"
                                            class="text-xs text-blue-600 hover:underline">
                                            Lihat Foto
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-slate-400 text-sm">
                                    Belum ada data presensi untuk hari ini.
                                </td>
                            </tr>
                        @endforelse
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
