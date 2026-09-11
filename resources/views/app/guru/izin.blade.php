@extends('layouts.layout')

@section('title', 'Pengajuan Izin Guru')
@section('page_title', 'Form Pengajuan Izin / Sakit')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Alert Notifikasi --}}
    @if (session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="border-b border-slate-100 pb-4 mb-5 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Form Surat Izin / Sakit</h3>
                <p class="text-xs text-slate-400 mt-0.5">Isi formulir berikut untuk mengajukan tidak hadir hari ini.</p>
            </div>
            <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full border border-blue-100">
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
            </span>
        </div>

        <form action="{{ route('guru.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Keterangan Alasan Izin --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Keterangan Alasan Izin <span class="text-rose-500">*</span>
                </label>
                <textarea name="keterangan" rows="4" required
                    placeholder="Tuliskan alasan izin/sakit secara rinci (misal: Sakit demam tinggi, Izin keperluan keluarga mendesak)..."
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                @error('keterangan')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload Foto Bukti --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Foto Bukti (Surat Dokter / Dokumen Pendukung) <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="bukti" accept="image/*" required
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
                <p class="text-[11px] text-slate-400 mt-1">Format gambar: JPG, PNG, JPEG (Maksimal 2MB).</p>
                @error('bukti')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-3 pt-3">
                <a href="{{ route('guru.dashboard') }}"
                    class="w-1/2 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition text-center">
                    Batal
                </a>
                <button type="submit"
                    class="w-1/2 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Pengajuan Izin</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
