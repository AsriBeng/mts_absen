@php
    $roleName = strtolower(Auth::user()->role->name ?? 'guru');
@endphp

<nav class="px-3 space-y-1">
    @if ($roleName === 'admin')
        {{-- Menu Admin --}}
        <a href="{{ url('/admin/dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/dashboard*') ? 'bg-emerald-600 text-white shadow-md' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white' }}">
            <i class="fas fa-chart-pie text-lg w-6 text-center shrink-0"></i>
            <span class="whitespace-nowrap" x-show="!sidebarMinimized">Dashboard</span>
        </a>

        <a href="{{ url('/admin/absensi') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-emerald-100 hover:bg-emerald-800/60 hover:text-white">
            <i class="fas fa-qrcode text-lg w-6 text-center shrink-0"></i>
            <span class="whitespace-nowrap" x-show="!sidebarMinimized">Absensi</span>
        </a>

        <a href="{{ url('/admin/rekap') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-emerald-100 hover:bg-emerald-800/60 hover:text-white">
            <i class="fas fa-file-alt text-lg w-6 text-center shrink-0"></i>
            <span class="whitespace-nowrap" x-show="!sidebarMinimized">Rekap</span>
        </a>

        <a href="{{ url('/admin/users-setting') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-emerald-100 hover:bg-emerald-800/60 hover:text-white">
            <i class="fas fa-user-cog text-lg w-6 text-center shrink-0"></i>
            <span class="whitespace-nowrap" x-show="!sidebarMinimized">Users Setting</span>
        </a>

    @else
        {{-- Menu Guru --}}
        <a href="{{ url('/guru/dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('guru/dashboard*') ? 'bg-emerald-600 text-white shadow-md' : 'text-emerald-100 hover:bg-emerald-800/60 hover:text-white' }}">
            <i class="fas fa-chart-pie text-lg w-6 text-center shrink-0"></i>
            <span class="whitespace-nowrap" x-show="!sidebarMinimized">Dashboard</span>
        </a>

        <a href="#"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-emerald-100 hover:bg-emerald-800/60 hover:text-white">
            <i class="fas fa-file-alt text-lg w-6 text-center shrink-0"></i>
            <span class="whitespace-nowrap" x-show="!sidebarMinimized">Absen Saya</span>
        </a>
    @endif
</nav>
