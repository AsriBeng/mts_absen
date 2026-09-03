@extends('layouts.layout')

@section('title', 'Dashboard Guru')
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

    {{-- Grid Kartu Presensi (Sama Fleksibelnya Seperti Admin) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Card Action: Scan Barcode --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center">
            <a href="#" class="inline-flex flex-col items-center justify-center w-32 h-32 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-full shadow-lg shadow-emerald-600/20 border-4 border-emerald-100 transition duration-200">
                <i class="fas fa-qrcode text-3xl mb-1"></i>
                <span class="text-[11px] font-bold uppercase tracking-wider">Scan Barcode</span>
            </a>
            <p class="text-xs text-slate-400 mt-3">Arahkan kamera ke QR Code papan sekolah</p>
        </div>

        {{-- Status Log Masuk & Pulang --}}
        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Absen Masuk --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Absen Masuk</span>
                    <p class="text-2xl font-bold text-slate-800 mt-1">07:15:20</p>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full text-xs font-bold">
                        Hadir
                    </span>
                    <span class="text-[11px] text-slate-400">Terhitung Tepat Waktu</span>
                </div>
            </div>

            {{-- Absen Pulang --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Absen Pulang</span>
                    <p class="text-2xl font-bold text-slate-400 mt-1">-- : --</p>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">
                        Belum Absen
                    </span>
                    <span class="text-[11px] text-slate-400">Jam Pulang: 14:00</span>
                </div>
            </div>

        </div>

    </div>

    {{-- Section Riwayat Terakhir --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Riwayat Terakhir</h3>
            <a href="#" class="text-xs font-semibold text-emerald-600 hover:underline">Lihat Semua</a>
        </div>

        <div class="p-4">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60 flex items-center justify-between text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                        <i class="fas fa-check text-base"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Kemarin</p>
                        <p class="text-slate-400 mt-0.5">07:10 - 14:00 WIB</p>
                    </div>
                </div>
                <span class="text-emerald-600 font-semibold bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-xl">
                    Hadir
                </span>
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
