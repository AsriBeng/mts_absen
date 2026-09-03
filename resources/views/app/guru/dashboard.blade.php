@extends('layouts.layout')

@section('title', 'Dashboard Guru')
@section('page_title', 'Dashboard Utama')

@section('content')
    <div class="space-y-6" x-data="{
        showScanModal: false,
        step: 1,
        qrCodeValue: '',
        locationStatus: 'Mengambil lokasi...',
        latitude: '',
        longitude: '',
        isLocationReady: false,
        isCapturing: false,
        capturedImage: ''
    }">

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

            {{-- Card Action: Scan Barcode --}}
            <div
                class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center flex flex-col items-center justify-center">
                <button @click="showScanModal = true; step = 1; startScanner();"
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

        {{-- MODAL SCANNER & VERIFIKASI LOKASI + SELFIE --}}
        <div x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal()"></div>

            {{-- Content Modal --}}
            <div class="flex items-center justify-center min-h-screen p-4">
                <div
                    class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center z-10 border border-slate-100">

                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                            <i class="fas"
                                :class="step === 1 ? 'fa-qrcode text-emerald-600' : 'fa-camera text-emerald-600'"></i>
                            <span x-text="step === 1 ? 'Langkah 1: Scan QR Code' : 'Langkah 2: Selfie & Lokasi GPS'"></span>
                        </h3>
                        <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    {{-- TAHAP 1: AREA SCANNER QR --}}
                    <div x-show="step === 1">
                        <div class="relative bg-slate-900 rounded-2xl overflow-hidden mb-4">
                            <div id="reader" class="w-full h-72"></div>
                        </div>
                        <p class="text-xs text-slate-400">Arahkan kamera HP Anda tepat di dalam area kotak QR Code.</p>
                    </div>

                    {{-- TAHAP 2: VERIFIKASI SELFIE & TITIK LOKASI GPS --}}
                    <div x-show="step === 2" style="display: none;">
                        <div class="space-y-4">

                            {{-- Area Preview Selfie / Kamera WebCam --}}
                            <div class="relative bg-slate-900 rounded-2xl overflow-hidden h-64 border border-slate-200">
                                <video id="selfieVideo" autoplay playsinline class="w-full h-full object-cover"
                                    x-show="!capturedImage"></video>
                                <img :src="capturedImage" class="w-full h-full object-cover" x-show="capturedImage"
                                    style="display: none;">

                                <button type="button" @click="retakeSelfie()" x-show="capturedImage"
                                    class="absolute bottom-3 right-3 bg-slate-900/80 hover:bg-slate-900 text-white text-xs px-3 py-1.5 rounded-xl backdrop-blur-md border border-white/20 transition flex items-center gap-1.5">
                                    <i class="fas fa-redo"></i> Foto Ulang
                                </button>
                            </div>

                            {{-- Indicator GPS Lokasi Guru --}}
                            <div class="p-3.5 rounded-xl border text-left text-xs flex items-center gap-3"
                                :class="isLocationReady ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                                    'bg-amber-50 border-amber-200 text-amber-800'">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                    :class="isLocationReady ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600'">
                                    <i class="fas"
                                        :class="isLocationReady ? 'fa-location-dot' : 'fa-spinner fa-spin'"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold"
                                        x-text="isLocationReady ? 'Lokasi Berhasil Dideteksi' : 'Mencari Titik GPS...'"></p>
                                    <p class="text-[11px] opacity-80" x-text="locationStatus"></p>
                                </div>
                                <button type="button" @click="getLocation()" x-show="!isLocationReady"
                                    class="text-amber-700 underline font-bold text-[11px]">
                                    Coba Lagi
                                </button>
                            </div>

                            {{-- Tombol Ambil Selfie / Kirim Absensi --}}
                            <div class="pt-2">
                                <button type="button" x-show="!capturedImage" @click="takeSelfiePhoto()"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-semibold text-xs py-3 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fas fa-camera"></i>
                                    <span>Ambil Foto Selfie</span>
                                </button>

                                <button type="button" x-show="capturedImage" @click="submitAttendanceForm()"
                                    :disabled="!isLocationReady"
                                    :class="isLocationReady ?
                                        'bg-emerald-600 hover:bg-emerald-700 active:scale-95 cursor-pointer' :
                                        'bg-slate-300 cursor-not-allowed'"
                                    class="w-full text-white font-semibold text-xs py-3 rounded-xl transition flex items-center justify-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Kirim Presensi Sekarang</span>
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- Form Hidden ke Backend --}}
                    <form id="scanForm" action="{{ route('guru.absen_saya.scan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="qr_code" id="qr_code_input">
                        <input type="hidden" name="image" id="image_input">
                        <input type="hidden" name="latitude" id="latitude_input">
                        <input type="hidden" name="longitude" id="longitude_input">
                    </form>

                </div>
            </div>

        </div>

    </div>

    {{-- Script Library Kamera HTML5 QR Code & Geolocation JS --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrCode;
        let selfieStream = null;

        function getAlpineData() {
            return Alpine.$data(document.querySelector('[x-data]'));
        }

        function startScanner() {
            html5QrCode = new Html5Qrcode("reader");

            const qrboxFunction = function(viewfinderWidth, viewfinderHeight) {
                let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                let qrboxSize = Math.floor(minEdgeSize * 0.75);
                return {
                    width: qrboxSize,
                    height: qrboxSize
                };
            };

            const config = {
                fps: 15,
                qrbox: qrboxFunction,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };

            html5QrCode.start({
                    facingMode: "environment"
                },
                config,
                onScanSuccess
            ).catch(err => {
                console.error("Gagal membuka kamera: ", err);
                alert("Gagal mengaktifkan kamera. Berikan izin akses kamera pada browser Anda!");
            });
        }

        function stopScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => console.error(err));
            }
        }

        function onScanSuccess(decodedText) {
            stopScanner();
            document.getElementById("qr_code_input").value = decodedText;

            const data = getAlpineData();
            data.qrCodeValue = decodedText;
            data.step = 2; // Pindah ke Tahap 2 (Selfie & GPS)

            // Aktifkan Kamera Depan Selfie & Dapatkan GPS
            startSelfieCamera();
            getLocation();
        }

        function startSelfieCamera() {
            const video = document.getElementById("selfieVideo");
            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user"
                },
                audio: false
            }).then(stream => {
                selfieStream = stream;
                video.srcObject = stream;
            }).catch(err => {
                console.error("Gagal mengakses kamera selfie:", err);
                alert("Kamera depan tidak dapat diakses!");
            });
        }

        function stopSelfieCamera() {
            if (selfieStream) {
                selfieStream.getTracks().forEach(track => track.stop());
                selfieStream = null;
            }
        }

        function takeSelfiePhoto() {
            const video = document.getElementById("selfieVideo");
            let canvas = document.createElement("canvas");
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            let ctx = canvas.getContext("2d");
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = canvas.toDataURL("image/png");
            document.getElementById("image_input").value = imageData;

            const data = getAlpineData();
            data.capturedImage = imageData;
        }

        function retakeSelfie() {
            const data = getAlpineData();
            data.capturedImage = '';
            document.getElementById("image_input").value = '';
        }

        function getLocation() {
            const data = getAlpineData();
            data.isLocationReady = false;
            data.locationStatus = "Meminta akses titik koordinat GPS...";

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        document.getElementById("latitude_input").value = lat;
                        document.getElementById("longitude_input").value = lng;

                        data.latitude = lat;
                        data.longitude = lng;
                        data.isLocationReady = true;
                        data.locationStatus = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
                    },
                    (error) => {
                        console.error("Gagal mengambil GPS:", error);
                        data.isLocationReady = false;
                        if (error.code === error.PERMISSION_DENIED) {
                            data.locationStatus = "Akses lokasi ditolak! Harap aktifkan GPS HP Anda.";
                        } else {
                            data.locationStatus = "Gagal mengambil titik koordinat GPS.";
                        }
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                data.locationStatus = "Browser tidak mendukung Geolocation GPS.";
            }
        }

        function submitAttendanceForm() {
            stopSelfieCamera();
            document.getElementById("scanForm").submit();
        }

        function closeModal() {
            stopScanner();
            stopSelfieCamera();
            const data = getAlpineData();
            data.showScanModal = false;
            data.step = 1;
            data.capturedImage = '';
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
