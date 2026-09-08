@extends('layouts.layout')

@section('title', 'Profil Saya')
@section('page_title', 'Informasi Profil Saya')

@section('content')
    @php
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name ?? '');
        $isAdmin  = $roleName === 'admin';
        $guruData = $authUser->guru;

        // Logika penyusunan Nama Lengkap + Gelar
        $displayName = $guruData->nama_lengkap ?? null;
        if ($displayName) {
            $gelarDepan = $guruData->gelar_depan ? trim($guruData->gelar_depan) . ' ' : '';
            $gelarBelakang = $guruData->gelar_belakang ? ', ' . trim($guruData->gelar_belakang) : '';
            $formattedName = $gelarDepan . $displayName . $gelarBelakang;
        } else {
            $formattedName = $authUser->name; // Fallback jika belum isi nama lengkap
        }

        // Format Tempat Tanggal Lahir
        $ttl = '-';
        if ($guruData && ($guruData->tempat_lahir || $guruData->tanggal_lahir)) {
            $tempat = $guruData->tempat_lahir ?? '-';
            $tgl = $guruData->tanggal_lahir ? \Carbon\Carbon::parse($guruData->tanggal_lahir)->translatedFormat('d F Y') : '-';
            $ttl = $tempat . ', ' . $tgl;
        }
    @endphp

    <div class="space-y-6" x-data="{ showEditModal: false }">

        {{-- Alert Notifikasi --}}
        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
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
        <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl p-6 text-white shadow-md relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-5 z-10 text-center sm:text-left">

                {{-- Foto Profile / Logo Instansi --}}
                <div class="relative shrink-0">
                    @if ($isAdmin)
                        <img src="{{ asset('image/logo.png') }}" class="w-24 h-24 rounded-2xl border-2 border-emerald-300/60 object-cover bg-white p-1 shadow-xl">
                    @else
                        @if ($guruData && $guruData->foto_profile && Storage::disk('public')->exists($guruData->foto_profile))
                            <img src="{{ asset('storage/' . $guruData->foto_profile) }}" class="w-24 h-24 rounded-2xl border-2 border-emerald-300/60 object-cover bg-slate-100 shadow-xl">
                        @else
                            <div class="w-24 h-24 rounded-2xl bg-white/10 backdrop-blur-md border-2 border-emerald-300/60 flex items-center justify-center text-4xl font-extrabold uppercase shadow-xl text-emerald-100">
                                {{ substr($authUser->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                    @endif

                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-400 border-2 border-emerald-900 rounded-full flex items-center justify-center text-[10px] text-emerald-950 font-bold" title="Status Aktif">
                        <i class="fas fa-check"></i>
                    </div>
                </div>

                <div>
                    {{-- Nama Lengkap + Gelar --}}
                    <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                        <h3 class="text-2xl font-bold tracking-tight">{{ $formattedName }}</h3>
                        <span class="bg-emerald-900/50 backdrop-blur-md px-3 py-0.5 rounded-full uppercase tracking-wider font-semibold text-[10px] border border-emerald-400/40 text-emerald-200">
                            {{ $authUser->role->name ?? 'User' }}
                        </span>
                    </div>

                    {{-- Username dibawah Nama Lengkap --}}
                    <p class="text-xs text-emerald-200/90 font-medium mt-0.5">
                        <i class="fas fa-user-circle text-emerald-300 mr-1"></i> Username: <span class="font-mono">{{ $authUser->name }}</span>
                    </p>

                    <p class="text-xs text-emerald-100/80 mt-1 flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                        @if (!$isAdmin)
                            <span><i class="fas fa-id-badge text-emerald-300"></i> NIP: <strong class="font-mono">{{ $guruData->nip ?? '-' }}</strong></span>
                            <span class="opacity-40">•</span>
                        @endif
                        <span><i class="fas fa-envelope text-emerald-300"></i> {{ $authUser->email }}</span>
                    </p>
                </div>
            </div>

            {{-- Tombol Edit Profil Guru --}}
            @if (!$isAdmin)
                <button @click="showEditModal = true"
                    class="z-10 bg-white/15 hover:bg-white/25 active:scale-95 text-white font-semibold text-xs py-3 px-5 rounded-xl border border-white/20 backdrop-blur-md shadow-sm transition flex items-center gap-2 shrink-0 cursor-pointer">
                    <i class="fas fa-user-edit text-sm"></i>
                    <span>Edit Profil</span>
                </button>
            @endif

            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        {{-- Detail Informasi Profil --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Informasi Akun --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                    <h4 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span>Informasi Akun</span>
                        <i class="fas fa-shield-alt text-emerald-600"></i>
                    </h4>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Jabatan / Peran</span>
                            <p class="font-semibold text-slate-700 capitalize mt-0.5">
                                {{ $authUser->role->name ?? 'Pengguna' }} MA Al-Huda
                            </p>
                        </div>

                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Alamat Email</span>
                            <p class="font-semibold text-slate-700 mt-0.5">{{ $authUser->email }}</p>
                        </div>

                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Terdaftar Sejak</span>
                            <p class="font-semibold text-slate-700 mt-0.5">
                                {{ \Carbon\Carbon::parse($authUser->created_at)->translatedFormat('d F Y') }}
                            </p>
                        </div>

                        <div>
                            <span class="text-slate-400 font-medium uppercase tracking-wider block text-[10px]">Status Akun</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Detail Biodata Lengkap --}}
            <div class="lg:col-span-2 space-y-6">

                @if ($isAdmin)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fas fa-building text-emerald-600"></i>
                                <span>Informasi Instansi Sistem</span>
                            </h4>
                            <span class="text-xs text-slate-400">MA Al-Huda</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Username Admin</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $authUser->name }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Email Administrator</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $authUser->email }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 sm:col-span-2">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Hak Akses Sistem</span>
                                <p class="font-bold text-emerald-600 text-xs mt-0.5">Akses Penuh Kelola Absensi, Users Setting, Rekap, dan Konfigurasi Hari Kerja.</p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Biodata Lengkap Guru --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fas fa-address-card text-emerald-600"></i>
                                <span>Biodata Lengkap Guru</span>
                            </h4>
                            <span class="text-xs text-slate-400">MA Al-Huda</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Nama Lengkap & Gelar</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $formattedName }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">NIP / NUPTK</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $guruData->nip ?? '-' }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">NIK KTP</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $guruData->nik ?? '-' }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Jenis Kelamin</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">
                                    {{ ($guruData->jenis_kelamin ?? '') === 'L' ? 'Laki-Laki' : (($guruData->jenis_kelamin ?? '') === 'P' ? 'Perempuan' : '-') }}
                                </p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Tempat, Tanggal Lahir</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $ttl }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Agama</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $guruData->agama ?? 'Islam' }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 sm:col-span-2">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">No. Handphone / WA</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $guruData->no_hp ?? '-' }}</p>
                            </div>

                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 sm:col-span-2">
                                <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Alamat Tempat Tinggal</span>
                                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $guruData->alamat ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Catatan Keamanan --}}
                <div class="bg-amber-50 rounded-2xl border border-amber-200/80 p-5 flex items-start gap-4 text-amber-800 text-xs shadow-sm">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 font-bold text-base">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div>
                        <h5 class="font-bold text-amber-900 text-sm mb-1">Pengingat Keamanan Akun</h5>
                        <p class="leading-relaxed text-amber-800/90">
                            Pastikan Email dan No. HP selalu mutakhir untuk kebutuhan verifikasi presensi. Jika ingin mengganti password, silakan buka menu <strong>Pengaturan</strong>.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- MODAL EDIT PROFIL GURU --}}
        @if (!$isAdmin)
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

                <div class="flex items-center justify-center min-h-screen p-4">
                    <div x-show="showEditModal" class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 text-left z-10 border border-slate-100">

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-5">
                            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                                <i class="fas fa-user-edit text-emerald-600"></i>
                                <span>Edit Biodata Guru</span>
                            </h3>
                            <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 transition">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <form action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            {{-- Foto Profile --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Foto Profile</label>
                                <input type="file" name="foto_profile" accept="image/*"
                                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
                            </div>

                            {{-- Input Gelar Depan, Nama Lengkap, Gelar Belakang --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap & Gelar</label>
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                                    <input type="text" name="gelar_depan" placeholder="Gelar Depan (Dr.)" value="{{ old('gelar_depan', $guruData->gelar_depan ?? '') }}"
                                        class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="{{ old('nama_lengkap', $guruData->nama_lengkap ?? '') }}" required
                                        class="sm:col-span-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                                    <input type="text" name="gelar_belakang" placeholder="Gelar Belakang (S.Pd)" value="{{ old('gelar_belakang', $guruData->gelar_belakang ?? '') }}"
                                        class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                </div>
                            </div>

                            {{-- Input NIK & NIP --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">NIK (KTP)</label>
                                    <input type="text" name="nik" maxlength="16" value="{{ old('nik', $guruData->nik ?? '') }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">NIP / NUPTK</label>
                                    <input type="text" name="nip" value="{{ old('nip', $guruData->nip ?? '') }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                </div>
                            </div>

                            {{-- Input Tempat Lahir & Tanggal Lahir --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $guruData->tempat_lahir ?? '') }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $guruData->tanggal_lahir ?? '') }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                </div>
                            </div>

                            {{-- Jenis Kelamin & Agama --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                        <option value="">-- Pilih --</option>
                                        <option value="L" {{ (old('jenis_kelamin', $guruData->jenis_kelamin ?? '') === 'L') ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="P" {{ (old('jenis_kelamin', $guruData->jenis_kelamin ?? '') === 'P') ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Agama</label>
                                    <select name="agama" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                            <option value="{{ $agm }}" {{ (old('agama', $guruData->agama ?? 'Islam') === $agm) ? 'selected' : '' }}>{{ $agm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Email & No HP --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                                    <input type="email" name="email" value="{{ old('email', $authUser->email) }}" required
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">No. Handphone / WA</label>
                                    <input type="text" name="no_hp" value="{{ old('no_hp', $guruData->no_hp ?? '') }}"
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                </div>
                            </div>

                            {{-- Input Alamat --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                                <textarea name="alamat" rows="2"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition resize-none">{{ old('alamat', $guruData->alamat ?? '') }}</textarea>
                            </div>

                            {{-- Tombol Aksi --}}
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
        @endif

    </div>
@endsection
