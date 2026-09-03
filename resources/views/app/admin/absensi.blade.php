@extends('layouts.layout')

@section('title', 'Kelola Absensi')
@section('page_title', 'Kelola & Barcode Absensi')

@section('content')
    <div class="space-y-6" x-data="{ showBarcodeModal: false }">

        {{-- Alert Notifikasi --}}
        @if (session('success'))
            <div
                class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        {{-- Banner Utama & Tombol Barcode --}}
        <div
            class="bg-emerald-900 text-white rounded-2xl p-6 shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold">Papan QR Code Absensi Kantor</h3>
                <p class="text-xs text-emerald-200 mt-1">Tampilkan QR Code ini di papan / monitor kantor agar guru dapat
                    melakukan presensi.</p>
            </div>

            <button @click="showBarcodeModal = true"
                class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-sm px-5 py-3 rounded-xl shadow-lg shadow-emerald-950/30 flex items-center gap-2 transition shrink-0 cursor-pointer">
                <i class="fas fa-qrcode text-lg"></i>
                <span>Buka Barcode Absensi</span>
            </button>
        </div>

        {{-- Tabel Riwayat Absensi Hari Ini --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Daftar Absensi Hari Ini</h3>
                <span
                    class="text-xs bg-emerald-50 text-emerald-600 font-bold px-3 py-1 rounded-full border border-emerald-100">{{ date('d M Y') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs text-slate-400 uppercase border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Nama Guru</th>
                            <th class="px-6 py-3 font-semibold">Jam Masuk</th>
                            <th class="px-6 py-3 font-semibold">Jam Pulang</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold text-center">Foto Selfie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $item->user->name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->time_in ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->time_out ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->status === 'hadir' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($item->image_in)
                                        <button class="text-xs text-emerald-600 hover:underline font-semibold">Lihat
                                            Foto</button>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">
                                    Belum ada data presensi hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL BARCODE ABSENSI --}}
        <div x-show="showBarcodeModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            {{-- Backdrop --}}
            <div x-show="showBarcodeModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showBarcodeModal = false"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            {{-- Isi Modal --}}
            <div class="flex items-center justify-center min-h-screen p-4">
                <div x-show="showBarcodeModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center z-10 border border-slate-100">

                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="font-bold text-slate-800 text-base">QR Code Absensi</h3>
                        <button @click="showBarcodeModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    {{-- Gambar Barcode / QR Code --}}
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 inline-block mb-3">
                        @php
                            $code = $activeKey->key_code ?? 'KNT-ALHUDA-DEFAULT';
                            $qrUrl =
                                'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($code);
                        @endphp
                        <img id="qrImage" src="{{ $qrUrl }}" alt="QR Code Absensi"
                            class="w-48 h-48 mx-auto object-contain rounded-lg shadow-sm">
                    </div>

                    {{-- Teks Kode Unik --}}
                    <p
                        class="text-xs font-mono bg-slate-100 py-1.5 px-3 rounded-lg text-slate-600 font-semibold mb-6 inline-block">
                        {{ $code }}
                    </p>

                    {{-- 2 Tombol Aksi: Generate & Download Langsung --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Form Generate Baru --}}
                        <form action="{{ route('admin.absensi.generate') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs py-3 px-3 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-sync-alt"></i> Generate
                            </button>
                        </form>

                        {{-- Tombol Download Langsung via JavaScript --}}
                        <button type="button"
                            onclick="downloadQRCode('{{ $qrUrl }}', 'QR_Absensi_{{ $code }}.png')"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs py-3 px-3 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Script JS untuk Download Otomatis Gambar QR --}}
    <script>
        async function downloadQRCode(imageUrl, fileName) {
            try {
                const response = await fetch(imageUrl);
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();

                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                console.error('Gagal mengunduh gambar QR:', error);
                alert('Gagal mengunduh gambar. Silakan coba lagi.');
            }
        }
    </script>
@endsection
