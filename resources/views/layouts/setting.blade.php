@extends('layouts.layout')

@section('title', 'Pengaturan Keamanan')
@section('page_title', 'Keamanan & Kebijakan Sandi')

@section('content')
    <div class="space-y-6" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">

        {{-- Alert Notifikasi Success --}}
        @if (session('success'))
            <div
                class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()"
                    class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        {{-- Alert Error --}}
        @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl shadow-sm text-sm space-y-1">
                <p class="font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Gagal memperbarui password:
                </p>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Form Utama Ubah Password --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <i class="fas fa-key text-emerald-600"></i>
                        <span>Perbarui Kata Sandi Akun</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Ganti kata sandi secara berkala untuk menjaga keamanan data akun presensi Anda di MA Al-Huda.
                    </p>
                </div>

                @php
                    $roleName = strtolower(Auth::user()->role->name ?? 'guru');
                    $updatePasswordRoute =
                        $roleName === 'admin' ? route('admin.password.update') : route('guru.password.update');
                @endphp

                <form action="{{ $updatePasswordRoute }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Password Saat Ini --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" required
                                placeholder="Masukkan password lama Anda"
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            <button type="button" @click="showCurrent = !showCurrent"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="fas fa-shield-alt text-sm"></i>
                            </span>
                            <input :type="showNew ? 'text' : 'password'" name="password" required
                                placeholder="Minimal 8 karakter kombinasi"
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            <button type="button" @click="showNew = !showNew"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="fas fa-check-double text-sm"></i>
                            </span>
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                                placeholder="Ulangi password baru Anda"
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-semibold text-xs py-3 px-6 rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i>
                            <span>Simpan Password Baru</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Panel Kebijakan Keamanan Instansi --}}
            <div class="space-y-6">

                {{-- Kartu Standar Kebijakan Password --}}
                <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-sm space-y-4">
                    <h4 class="font-bold text-sm border-b border-slate-800 pb-3 flex items-center gap-2 text-emerald-400">
                        <i class="fas fa-user-shield"></i>
                        <span>Kebijakan Sandi/Password</span>
                    </h4>

                    <ul class="space-y-2.5 text-xs text-slate-300">
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
                            <span>Panjang sandi **minimal 8 karakter**.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
                            <span>Mengandung kombinasi **huruf besar, huruf kecil, dan angka**.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
                            <span>Tidak menggunakan tanggal lahir atau kata acak umum.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
                            <span>Dilarang membagikan password akun presensi kepada siapapun.</span>
                        </li>
                    </ul>
                </div>

                {{-- Kartu Catatan Bantuan --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-2 text-xs">
                    <h5 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                        <i class="fas fa-headset text-emerald-600"></i>
                        <span>Butuh Bantuan Akun?</span>
                    </h5>
                    <p class="text-slate-500 leading-relaxed">
                        Jika Anda lupa password lama atau akun mengalami kendala akses, silakan hubungi <strong>Tim IT /
                            Admin MA Al-Huda</strong> untuk reset kata sandi.
                    </p>
                </div>

            </div>

        </div>

    </div>
@endsection
