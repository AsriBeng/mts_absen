<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Presensi') - MA Al-Huda</title>

    {{-- Tailwind CSS & FontAwesome --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col"
      x-data="{
          sidebarMinimized: window.innerWidth >= 768 && window.innerWidth < 1024,
          mobileSidebarOpen: false
      }"
      x-init="
          window.addEventListener('resize', () => {
              if (window.innerWidth >= 768 && window.innerWidth < 1024) {
                  sidebarMinimized = true;
              } else if (window.innerWidth >= 1024) {
                  sidebarMinimized = false;
              }
              if (window.innerWidth >= 768) {
                  mobileSidebarOpen = false;
              }
          });
      ">

    @php
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name ?? '');
        $isAdmin  = $roleName === 'admin';
        $guruData = $authUser->guru;

        // 1. Foto Avatar Header Dinamis
        $avatarUrl = asset('image/logo.png'); // Default foto instansi untuk Admin
        if (!$isAdmin && $guruData && $guruData->foto_profile && \Illuminate\Support\Facades\Storage::disk('public')->exists($guruData->foto_profile)) {
            $avatarUrl = asset('storage/' . $guruData->foto_profile);
        }

        // 2. Format Nama Tampilan: Gelar Depan + Nama Lengkap + Gelar Belakang
        if ($isAdmin) {
            $headerDisplayName = $authUser->name;
        } else {
            $displayName = $guruData->nama_lengkap ?? null;
            if ($displayName) {
                $gelarDepan = $guruData->gelar_depan ? trim($guruData->gelar_depan) . ' ' : '';
                $gelarBelakang = $guruData->gelar_belakang ? ', ' . trim($guruData->gelar_belakang) : '';
                $headerDisplayName = $gelarDepan . $displayName . $gelarBelakang;
            } else {
                $headerDisplayName = $authUser->name; // Fallback jika belum diisi
            }
        }
    @endphp

    {{-- HEADER UTAMA --}}
    <header class="h-20 bg-emerald-900 text-white px-4 sm:px-6 flex items-center justify-between border-b border-emerald-950 shadow-md sticky top-0 z-30 shrink-0">

        {{-- Sisi Kiri Header: Logo + Instansi + Toggle + Title --}}
        <div class="flex items-center gap-3 sm:gap-4">
            {{-- Logo & Nama Instansi --}}
            <div class="flex items-center gap-3 pr-2 border-r border-emerald-800/80">
                <img src="{{ asset('image/logo.png') }}" alt="Logo MA Al-Huda" class="w-10 h-10 object-contain shrink-0 rounded-lg bg-white/10 p-1">
                <div class="hidden sm:block">
                    <h1 class="font-bold text-sm text-white leading-tight">MA Al-Huda</h1>
                    <p class="text-[11px] text-emerald-300 capitalize font-medium tracking-wider">
                        {{ $authUser->role->name ?? 'User' }} Panel
                    </p>
                </div>
            </div>

            {{-- Tombol Toggle Sidebar --}}
            <button @click="if (window.innerWidth < 768) { mobileSidebarOpen = !mobileSidebarOpen } else { sidebarMinimized = !sidebarMinimized }"
                    class="p-2 rounded-xl text-emerald-200 hover:bg-emerald-800 hover:text-white transition outline-none"
                    title="Toggle Sidebar">
                <i class="fas fa-bars text-lg"></i>
            </button>

            {{-- Nama Halaman Aktif --}}
            <h2 class="text-sm sm:text-lg font-bold text-white truncate max-w-37.5 sm:max-w-xs">
                @yield('page_title', 'Dashboard')
            </h2>
        </div>

        {{-- Sisi Kanan Header: Profile Card & Dropdown Menu --}}
        <div class="relative" x-data="{ profileDropdownOpen: false }">

            {{-- Kartu Profil (Trigger Dropdown) --}}
            <button @click="profileDropdownOpen = !profileDropdownOpen"
                    @click.away="profileDropdownOpen = false"
                    class="flex items-center gap-3 bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-2xl border border-emerald-700/50 transition focus:outline-none cursor-pointer">

                {{-- Role / Jabatan & Nama Lengkap User --}}
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-white leading-tight mt-0.5">
                        {{ $headerDisplayName }}
                    </p>
                    <p class="text-[11px] text-emerald-200 font-medium capitalize leading-none">
                        {{ $authUser->role->name ?? 'Guru' }}
                    </p>
                </div>

                {{-- Foto User / Avatar Dinamis --}}
                <img src="{{ $avatarUrl }}"
                     alt="User Avatar"
                     class="w-9 h-9 rounded-xl border border-emerald-400 object-cover bg-white p-0.5 shadow-sm shrink-0">
            </button>

            {{-- DROPDOWN MENU PROFIL --}}
            <div x-show="profileDropdownOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                 class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 text-slate-700"
                 style="display: none;">

                @php
                    $profileRoute = $isAdmin ? route('admin.profile') : route('guru.profile');
                    $settingRoute = $isAdmin ? route('admin.setting') : route('guru.setting');
                @endphp

                {{-- Item Profil --}}
                <a href="{{ $profileRoute }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium hover:bg-slate-50 hover:text-emerald-600 transition">
                    <i class="far fa-user text-base w-5 text-center text-slate-400"></i>
                    <span>Profil</span>
                </a>

                {{-- Item Pengaturan --}}
                <a href="{{ $settingRoute }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium hover:bg-slate-50 hover:text-emerald-600 transition">
                    <i class="fas fa-cog text-base w-5 text-center text-slate-400"></i>
                    <span>Pengaturan</span>
                </a>

                <hr class="my-1 border-slate-100">

                {{-- Item Keluar / Logout --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer">
                        <i class="fas fa-sign-out-alt text-base w-5 text-center"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>

        </div>
    </header>

    {{-- KONTEN UTAMA + SIDEBAR CONTAINER --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- SIDEBAR DESKTOP & TABLET --}}
        <aside class="hidden md:flex flex-col bg-emerald-950 text-white shrink-0 transition-all duration-300 border-r border-emerald-900"
               :class="sidebarMinimized ? 'w-20' : 'w-64'">
            <div class="flex-1 overflow-y-auto py-2">
                @include('layouts.menu')
            </div>
            <div class="p-4 border-t border-emerald-900/80 text-center text-xs text-emerald-400" x-show="!sidebarMinimized">
                &copy; {{ date('Y') }} MA Al-Huda
            </div>
        </aside>

        {{-- SIDEBAR MOBILE --}}
        <div x-show="mobileSidebarOpen" class="relative z-40 md:hidden" style="display: none;">
            <div x-show="mobileSidebarOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileSidebarOpen = false"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div class="fixed inset-y-0 left-0 max-w-xs w-full bg-emerald-950 text-white shadow-2xl flex flex-col justify-between"
                 x-show="mobileSidebarOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full">

                <div>
                    <div class="h-20 px-6 bg-emerald-900 flex items-center justify-between border-b border-emerald-800">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                            <span class="font-bold text-sm text-white">MA Al-Huda</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="text-emerald-300 hover:text-white">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="py-4">
                        @include('layouts.menu')
                    </div>
                </div>

                <div class="p-4 border-t border-emerald-900 text-center text-xs text-emerald-400">
                    &copy; {{ date('Y') }} MA Al-Huda
                </div>
            </div>
        </div>

        {{-- AREA CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-50">
            @yield('content')
        </main>
    </div>

</body>
</html>
