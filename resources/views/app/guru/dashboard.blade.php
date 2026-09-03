@extends('layouts.layout')

@section('title', 'Dashboard Guru')
@section('page_title', 'Dashboard Utama')

@section('content')
    <div class="space-y-6" x-data="{ showScanModal: false }">

        {{-- Alert Notifikasi Success / Error --}}
        @if (session('success'))
            <div
                class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
            </div>
        @endif

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

        {{-- Grid Kartu Presensi --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Card Action: Scan Barcode (Aktif dengan Trigger Modal) --}}
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center">
                <button @click="showScanModal = true; startScanner();"
                    class="inline-flex flex-col items-center justify-center w-32 h-32 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-full shadow-lg shadow-emerald-600/20 border-4 border-emerald-100 transition duration-200 cursor-pointer">
                    <i class="fas fa-qrcode text-3xl mb-1"></i>
                    <span class="text-[11px] font-bold uppercase tracking-wider">Scan Barcode</span>
                </button>
                <p class="text-xs text-slate-400 mt-3">Arahkan kamera ke QR Code papan sekolah</p>
            </div>

            {{-- Status Log Masuk & Pulang Dinamis --}}
            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Absen Masuk --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Absen Masuk</span>
                        <p class="text-2xl font-bold text-slate-800 mt-1">
                            {{ isset($todayAttendance->time_in) ? \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i:s') : '-- : --' }}
                        </p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        @if (isset($todayAttendance->time_in))
                            <span
                                class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full text-xs font-bold">
                                {{ ucfirst($todayAttendance->status) }}
                            </span>
                            <span class="text-[11px] text-slate-400">Tercatat di Server</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">
                                Belum Absen
                            </span>
                            <span class="text-[11px] text-slate-400">Jam Masuk: 07:00</span>
                        @endif
                    </div>
                </div>

                {{-- Absen Pulang --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Absen Pulang</span>
                        <p
                            class="text-2xl font-bold {{ isset($todayAttendance->time_out) ? 'text-slate-800' : 'text-slate-400' }} mt-1">
                            {{ isset($todayAttendance->time_out) ? \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i:s') : '-- : --' }}
                        </p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        @if (isset($todayAttendance->time_out))
                            <span
                                class="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-full text-xs font-bold">
                                Sudah Pulang
                            </span>
                            <span class="text-[11px] text-slate-400">Selesai Kerja</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">
                                Belum Absen
                            </span>
                            <span class="text-[11px] text-slate-400">Jam Pulang: 14:00</span>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        {{-- Section Riwayat Terakhir --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Riwayat Presensi Terakhir</h3>
                <a href="{{ route('guru.absen_saya') }}"
                    class="text-xs font-semibold text-emerald-600 hover:underline">Lihat Semua</a>
            </div>

            <div class="p-4 space-y-3">
                @forelse($recentAttendances ?? [] as $item)
                    <div
                        class="bg-slate-50 p-4 rounded-xl border border-slate-200/60 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                                <i class="fas fa-check text-base"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm">
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                                </p>
                                <p class="text-slate-400 mt-0.5">
                                    {{ $item->time_in ?? '-' }} - {{ $item->time_out ?? 'Belum Pulang' }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-emerald-600 font-semibold bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-xl">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat presensi.</p>
                @endforelse
            </div>
        </div>

        {{-- MODAL SCANNER QR CODE --}}
        <div x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="stopScanner(); showScanModal = false;">
            </div>

            {{-- Content Modal --}}
            <div class="flex items-center justify-center min-h-screen p-4">
                <div
                    class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center z-10 border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="font-bold text-slate-800 text-base">Pemindai QR Absensi</h3>
                        <button @click="stopScanner(); showScanModal = false;" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    {{-- Area Kamera Scanner --}}
                    <div class="relative bg-slate-900 rounded-2xl overflow-hidden mb-4">
                        <div id="reader" class="w-full h-72"></div>
                    </div>

                    {{-- Form Hidden untuk Pengiriman Data ke Backend --}}
                    <form id="scanForm" action="{{ route('guru.absen_saya.scan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="qr_code" id="qr_code_input">
                        <input type="hidden" name="image" id="image_input">
                    </form>

                    <p class="text-xs text-slate-400">Posisikan QR Code berada di dalam area kotak pemindai.</p>
                </div>
            </div>

        </div>

    </div>

    {{-- Script Library Kamera HTML5 QR Code --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrCode;

        function startScanner() {
            html5QrCode = new Html5Qrcode("reader");

            // Kalkulasi ukuran qrbox responsif (75% dari dimensi terdeteksi kamera)
            const qrboxFunction = function(viewfinderWidth, viewfinderHeight) {
                let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                let qrboxSize = Math.floor(minEdgeSize * 0.75);
                return {
                    width: qrboxSize,
                    height: qrboxSize
                };
            };

            const config = {
                fps: 15, // Naikkan frame rate agar deteksi lebih responsif
                qrbox: qrboxFunction,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true // Gunakan bawaan native jika ada
                }
            };

            html5QrCode.start({
                    facingMode: "environment"
                },
                config,
                onScanSuccess
            ).catch(err => {
                console.error("Gagal membuka kamera: ", err);
                alert("Gagal mengaktifkan kamera. Pastikan izin kamera sudah diberikan!");
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => console.error(err));
            }
        }

        function onScanSuccess(decodedText) {
            // Ambil screenshot foto selfie dari video kamera
            const video = document.querySelector("#reader video");
            let canvas = document.createElement("canvas");
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            let ctx = canvas.getContext("2d");
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Simpan data Base64 gambar dan isi form
            document.getElementById("qr_code_input").value = decodedText;
            document.getElementById("image_input").value = canvas.toDataURL("image/png");

            // Matikan scanner & kirim form secara otomatis
            stopScanner();
            document.getElementById("scanForm").submit();
        }

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
