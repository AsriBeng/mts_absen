@extends('layouts.layout')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Utama')

@section('content')
    {{-- Tambahkan state x-data untuk kontrol modal preview photo --}}
    <div class="space-y-6" x-data="{ showPhotoModal: false, previewPhotoUrl: '' }">

        {{-- Widget Hari & Jam Digital Real-time --}}
        <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl p-6 text-white shadow-sm relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
                <div>
                    <p class="text-xs uppercase tracking-wider font-semibold text-emerald-200">
                        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                    <h3 class="text-3xl font-extrabold mt-1 tracking-wider">
                        <span id="realtime-clock">{{ date('H:i:s') }}</span>
                        <span class="text-sm font-normal">WIB</span>
                    </h3>
                </div>
                <div class="text-xs bg-emerald-900/40 backdrop-blur-md px-4 py-2 rounded-xl border border-emerald-400/30">
                    <i class="fas fa-info-circle mr-1 text-emerald-300"></i>
                    Status Hari Ini:
                    @if (($todaySetting->status ?? 'masuk') === 'libur')
                        <span class="font-bold text-rose-300">Hari Libur</span>
                    @else
                        <span class="font-bold text-emerald-200">Jam Kerja Normal</span>
                    @endif
                </div>
            </div>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
        </div>

        {{-- Card Informasi Aturan Jam Kerja & GPS Kantor Hari Ini --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fas fa-clock text-emerald-600"></i>
                    <span>Aturan Presensi Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }})</span>
                </h3>
                <span class="text-xs text-slate-400">Aturan dari database</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 text-xs">
                @if (($todaySetting->status ?? 'masuk') === 'libur')
                    {{-- Tampilan Saat Hari Libur --}}
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Jam Masuk</p>
                        <p class="text-sm font-bold text-slate-400 mt-1">-</p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Jam Pulang</p>
                        <p class="text-sm font-bold text-slate-400 mt-1">-</p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Toleransi</p>
                        <p class="text-sm font-bold text-slate-400 mt-1">-</p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Titik GPS Kantor</p>
                        <p class="text-sm font-bold text-slate-400 mt-1">-</p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 col-span-2 sm:col-span-1">
                        <p class="text-slate-400 font-medium">Radius Maksimal</p>
                        <p class="text-sm font-bold text-slate-400 mt-1">-</p>
                    </div>
                @else
                    {{-- Tampilan Saat Hari Kerja --}}
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Jam Masuk</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">
                            {{ isset($todaySetting->time_in) ? \Carbon\Carbon::parse($todaySetting->time_in)->format('H:i') . ' WIB' : '-' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Jam Pulang</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">
                            {{ isset($todaySetting->time_out) ? \Carbon\Carbon::parse($todaySetting->time_out)->format('H:i') . ' WIB' : '-' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Toleransi</p>
                        <p class="text-sm font-bold text-amber-600 mt-1">
                            {{ $todaySetting->late_tolerance_minutes ?? 0 }} Menit
                        </p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-slate-400 font-medium">Titik GPS Kantor</p>
                        <p class="text-xs font-mono font-bold text-slate-700 mt-1 truncate" title="{{ $todaySetting->office_latitude ?? 0 }}, {{ $todaySetting->office_longitude ?? 0 }}">
                            {{ $todaySetting->office_latitude ?? '0' }}, {{ $todaySetting->office_longitude ?? '0' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 col-span-2 sm:col-span-1">
                        <p class="text-slate-400 font-medium">Radius Maksimal</p>
                        <p class="text-sm font-bold text-emerald-600 mt-1">
                            {{ $todaySetting->radius_meters ?? 0 }} Meter
                        </p>
                    </div>
                @endif
            </div>
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
                            <th class="px-6 py-3 font-semibold text-center">Bukti</th>
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
                                    @if ($row->status == 'Hadir' || $row->status == 'hadir')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Hadir</span>
                                    @elseif($row->status == 'Terlambat' || $row->status == 'terlambat')
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">Terlambat</span>
                                    @elseif(in_array(strtolower($row->status), ['izin', 'sakit']))
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600">{{ ucfirst($row->status) }}</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600">{{ ucfirst($row->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $photoPath = $row->image_in ?? $row->image_path ?? null;
                                    @endphp

                                    @if ($photoPath)
                                        <button type="button"
                                            @click="previewPhotoUrl = '{{ Storage::url($photoPath) }}'; showPhotoModal = true;"
                                            class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline cursor-pointer">
                                            <i class="fas fa-image"></i>
                                            <span>Lihat Foto</span>
                                        </button>
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

        {{-- MODAL PREVIEW FOTO SELFIE MASUK --}}
        <div x-show="showPhotoModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showPhotoModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center z-10 border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                            <i class="fas fa-camera text-emerald-600"></i>
                            <span>Foto Selfie Masuk</span>
                        </h3>
                        <button type="button" @click="showPhotoModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    {{-- Container Foto Preview --}}
                    <div class="relative bg-slate-100 rounded-2xl overflow-hidden mb-4 max-h-80 flex items-center justify-center border border-slate-200">
                        <img :src="previewPhotoUrl" alt="Selfie Presensi" class="w-full h-auto object-contain max-h-80 rounded-2xl">
                    </div>

                    <button type="button" @click="showPhotoModal = false"
                        class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
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
