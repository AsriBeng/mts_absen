@extends('layouts.layout')

@section('title', 'Profil Saya')
@section('page_title', 'Informasi Profil Saya')

@section('content')
    <div class="space-y-6" x-data="{ showEditModal: false }">

        {{-- Alert Notifikasi --}}
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

        @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl shadow-sm text-sm space-y-1">
                <p class="font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memperbarui data:
                </p>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Banner Utama Profil --}}
        <div
            class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl p-6 text-white shadow-md relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-5 z-10 text-center sm:text-left">
                <div class="relative shrink-0">
                    <div
                        class="w-24 h-24 rounded-2xl bg-white/10 backdrop-blur-md border-2 border-emerald-300/60 flex items-center justify-center text-4xl font-extrabold uppercase shadow-xl text-emerald-100">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-400 border-2 border-emerald-900 rounded-full flex items-center justify-center text-[10px] text-emerald-950 font-bold"
                        title="Status Aktif">
                        <i class="fas fa-check"></i>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                        <h3 class="text-2xl font-bold tracking-tight">{{ Auth::user()->name }}</h3>
                        <span
                            class="bg-emerald-900/50 backdrop-blur-md px-3 py-0.5 rounded-full uppercase tracking-wider font-semibold text-[10px] border border-emerald-400/40 text-emerald-200">
                            {{ Auth::user()->role->name ?? 'User' }}
                        </span>
                    </div>
                    <p
                        class="text-xs text-emerald-100/80 mt-1 flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                        <span><i class="fas fa-id-badge text-emerald-300"></i> NIP: <strong
                                class="font-mono">{{ Auth::user()->nip ?? '-' }}</strong></span>
                        <span class="opacity-40">•</span>
                        <span><i class="fas fa-envelope text-emerald-300"></i> {{ Auth::user()->email }}</span>
                    </p>
                </div>
            </div>

            {{-- Tombol Trigger Modal Edit --}}
            <button @click="showEditModal = true"
                class="z-10 bg-white/15 hover:bg-white/25 active:scale-95 text-white font-semibold text-xs py-3 px-5 rounded-xl border border-white/20 backdrop-blur-md shadow-sm transition flex items-center gap-2 shrink-0 cursor-pointer">
                <i class="fas fa-user-edit text-sm"></i>
                <span>Edit Profil</span>
            </button>

            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        {{-- Detail Informasi Profil (Grid Layout) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Kartu Status Akun --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                    <h4
                        class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span>Informasi Akun</span>
                        <i class="fas fa-shield-alt text-emerald-600"></i>
                    </h4>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Jabatan /
                                Peran</span>
                            <p class="font-semibold text-slate-700 capitalize mt-0.5">
                                {{ Auth::user()->role->name ?? 'Guru' }} MA Al-Huda</p>
                        </div>

                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Alamat
                                Email</span>
                            <p class="font-semibold text-slate-700 mt-0.5">{{ Auth::user()->email }}</p>
                        </div>

                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Terdaftar
                                Sejak</span>
                            <p class="font-semibold text-slate-700 mt-0.5">
                                {{ \Carbon\Carbon::parse(Auth::user()->created_at)->translatedFormat('d F Y') }}</p>
                        </div>

                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Status
                                Akun</span>
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Detail Biodata Selaras --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Kartu Detail Biodata Lengkap --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fas fa-address-card text-emerald-600"></i>
                            <span>Biodata Lengkap Pegawai</span>
                        </h4>
                        <span class="text-xs text-slate-400">MA Al-Huda</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Nama
                                Lengkap</span>
                            <p class="font-bold text-slate-800 text-sm mt-0.5">{{ Auth::user()->name }}</p>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Nomor
                                Induk Pegawai (NIP)</span>
                            <p class="font-bold text-slate-800 text-sm font-mono mt-0.5">
                                {{ Auth::user()->nip ?? 'Belum Diatur' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Alamat
                                Email</span>
                            <p class="font-bold text-slate-800 text-sm mt-0.5">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                            <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Nomor HP /
                                WhatsApp</span>
                            <p class="font-bold text-slate-800 text-sm mt-0.5">{{ Auth::user()->phone ?? 'Belum Diatur' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Catatan Keamanan --}}
                <div
                    class="bg-amber-50 rounded-2xl border border-amber-200/80 p-5 flex items-start gap-4 text-amber-800 text-xs shadow-sm">
                    <div
                        class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 font-bold text-base">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div>
                        <h5 class="font-bold text-amber-900 text-sm mb-1">Pengingat Keamanan Akun</h5>
                        <p class="leading-relaxed text-amber-800/90">
                            Pastikan NIP, Email, dan No. HP selalu mutakhir untuk kebutuhan verifikasi presensi. Jika ingin
                            mengganti password, silakan buka menu <strong>Pengaturan Keamanan</strong>.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- MODAL POP-UP EDIT PROFIL (SELARAS DENGAN BIODATA) --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            {{-- Backdrop --}}
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            {{-- Container Modal --}}
            <div class="flex items-center justify-center min-h-screen p-4">
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 text-left z-10 border border-slate-100">

                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-5">
                        <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                            <i class="fas fa-user-edit text-emerald-600"></i>
                            <span>Edit Biodata Pegawai</span>
                        </h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    @php
                        $roleName = strtolower(Auth::user()->role->name ?? 'guru');
                        $updateProfileRoute =
                            $roleName === 'admin' ? route('admin.profile.update') : route('guru.profile.update');
                    @endphp

                    {{-- Form Edit Selaras (4 Field) --}}
                    <form action="{{ $updateProfileRoute }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Input Nama --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                Nama Lengkap & Gelar
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            </div>
                        </div>

                        {{-- Input NIP --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                Nomor Induk Pegawai (NIP)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="fas fa-id-badge"></i>
                                </span>
                                <input type="text" name="nip" value="{{ old('nip', Auth::user()->nip) }}"
                                    placeholder="Contoh: 198501012010011001"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            </div>
                        </div>

                        {{-- Input Email --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                Alamat Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            </div>
                        </div>

                        {{-- Input Nomor HP --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                                Nomor HP / WhatsApp
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                            </div>
                        </div>

                        {{-- Tombol Aksi Modal --}}
                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 mt-6">
                            <button type="button" @click="showEditModal = false"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer">
                                <i class="fas fa-save"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection
