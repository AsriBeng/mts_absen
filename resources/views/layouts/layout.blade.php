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
          mobileSidebarOpen: false,
          profileDrawerOpen: false
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

    {{-- HEADER UTAMA (FULL WIDTH DI ATAS) --}}
    <header class="h-20 bg-emerald-900 text-white px-4 sm:px-6 flex items-center justify-between border-b border-emerald-950 shadow-md sticky top-0 z-30 shrink-0">

        {{-- Sisi Kiri Header: Logo + Instansi + Toggle + Title --}}
        <div class="flex items-center gap-3 sm:gap-4">

            {{-- Logo & Nama Instansi --}}
            <div class="flex items-center gap-3 pr-2 border-r border-emerald-800/80">
                <img src="{{ asset('image/logo.png') }}" alt="Logo MA Al-Huda" class="w-10 h-10 object-contain shrink-0 rounded-lg bg-white/10 p-1">
                <div class="hidden sm:block">
                    <h1 class="font-bold text-sm text-white leading-tight">MA Al-Huda</h1>
                    <p class="text-[11px] text-emerald-300 capitalize font-medium tracking-wider">
                        {{ Auth::user()->role->name ?? 'User' }} Panel
                    </p>
                </div>
            </div>

            {{-- Tombol Toggle Sidebar (Responsive Behavior) --}}
            <button @click="if (window.innerWidth < 768) { mobileSidebarOpen = !mobileSidebarOpen } else { sidebarMinimized = !sidebarMinimized }"
                    class="p-2 rounded-xl text-emerald-200 hover:bg-emerald-800 hover:text-white transition outline-none"
                    title="Toggle Sidebar">
                <i class="fas fa-bars text-lg"></i>
            </button>

            {{-- Nama Halaman Aktif --}}
            <h2 class="text-sm sm:text-lg font-bold text-white truncate max-w- sm:max-w-xs">
                @yield('page_title', 'Dashboard')
            </h2>
        </div>

        {{-- Sisi Kanan Header: Nama User & Icon Profil --}}
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-emerald-100 hidden md:inline-block">
                {{ Auth::user()->name ?? 'User' }}
            </span>

            <button @click="profileDrawerOpen = true"
                    class="relative group focus:outline-none focus:ring-2 focus:ring-emerald-400 rounded-full">
                <img src="{{ asset('image/logo.png') }}"
                     alt="User Avatar"
                     class="w-10 h-10 rounded-full border-2 border-emerald-400 p-0.5 object-cover bg-emerald-50 hover:opacity-90 transition">
            </button>
        </div>
    </header>

    {{-- KONTEN UTAMA + SIDEBAR CONTAINER (DI BAWAH HEADER) --}}
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

        {{-- SIDEBAR MOBILE (DRAWER OFF-CANVAS SLIDE-OVER) --}}
        <div x-show="mobileSidebarOpen" class="relative z-40 md:hidden" style="display: none;">
            {{-- Backdrop --}}
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
                    {{-- Header Sidebar Mobile --}}
                    <div class="h-20 px-6 bg-emerald-900 flex items-center justify-between border-b border-emerald-800">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                            <span class="font-bold text-sm text-white">MA Al-Huda</span>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="text-emerald-300 hover:text-white">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    {{-- Menu Mobile --}}
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

    {{-- SLIDE-OVER PROFILE DRAWER --}}
    <div x-show="profileDrawerOpen" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <div x-show="profileDrawerOpen"
             x-transition:enter="ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="profileDrawerOpen = false"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="profileDrawerOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-sm bg-white shadow-2xl flex flex-col justify-between">

                <div>
                    <div class="p-6 bg-emerald-700 text-white flex items-center justify-between">
                        <h3 class="font-bold text-base">Profil Pengguna</h3>
                        <button @click="profileDrawerOpen = false" class="text-emerald-100 hover:text-white">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 text-center border-b border-slate-100">
                        <img src="{{ asset('image/logo.png') }}"
                             alt="Avatar"
                             class="w-20 h-20 rounded-full border-4 border-emerald-100 mx-auto mb-3 object-cover bg-emerald-50 p-1">
                        <h4 class="font-bold text-slate-800 text-base">{{ Auth::user()->name ?? 'User' }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">{{ Auth::user()->email ?? 'user@email.com' }}</p>
                        <span class="inline-block mt-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-semibold capitalize">
                            {{ Auth::user()->role->name ?? 'User' }}
                        </span>
                    </div>

                    <div class="p-4 space-y-1">
                        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">
                            <i class="fas fa-user-circle w-5 text-emerald-600"></i> Profile
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">
                            <i class="fas fa-sliders-h w-5 text-emerald-600"></i> Setting
                        </a>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl font-semibold text-sm transition">
                            <i class="fas fa-sign-out-alt"></i> Log Out
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
