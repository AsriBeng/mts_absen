<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MA Al-Huda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="min-h-screen flex flex-col md:flex-row">
        {{-- Sidebar --}}
        <aside class="w-full md:w-64 bg-slate-900 text-slate-200 flex-shrink-0">
            <div class="p-4 border-b border-slate-800 flex items-center gap-3">
                <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-9 h-9 object-contain">
                <div>
                    <h1 class="font-bold text-sm text-white leading-tight">MA Al-Huda</h1>
                    <p class="text-xs text-slate-400">Admin Panel</p>
                </div>
            </div>
            <nav class="p-4 space-y-1 text-sm font-medium">
                <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-emerald-600 text-white shadow-sm">
                    <i class="fas fa-chart-pie w-5"></i> Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-slate-200 transition">
                    <i class="fas fa-qrcode w-5"></i> Barcode Kantor
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-slate-200 transition">
                    <i class="fas fa-clock w-5"></i> Pengaturan Jam
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-slate-200 transition">
                    <i class="fas fa-users w-5"></i> Data Guru
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-slate-200 transition">
                    <i class="fas fa-file-alt w-5"></i> Rekap Absensi
                </a>
            </nav>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col">
            {{-- Header Topbar --}}
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-800">Dashboard Pemantauan</h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600 font-medium">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold px-3 py-2 rounded-lg transition">
                            <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                        </button>
                    </form>
                </div>
            </header>

            <div class="p-6 space-y-6 flex-1">
                {{-- Card Ringkasan Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Hadir Tepat Waktu</p>
                            <h3 class="text-2xl font-bold text-emerald-600 mt-1">12</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Terlambat</p>
                            <h3 class="text-2xl font-bold text-amber-500 mt-1">3</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Izin / Sakit</p>
                            <h3 class="text-2xl font-bold text-blue-500 mt-1">1</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tanpa Keterangan</p>
                            <h3 class="text-2xl font-bold text-rose-500 mt-1">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>

                {{-- Tabel Presensi Hari Ini --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">Presensi Guru Hari Ini</h3>
                        <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium">{{ date('d M Y') }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs text-slate-400 uppercase border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3 font-semibold">Nama Guru</th>
                                    <th class="px-6 py-3 font-semibold">Jam Masuk</th>
                                    <th class="px-6 py-3 font-semibold">Jam Pulang</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                    <th class="px-6 py-3 font-semibold text-center">Selfie</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 font-medium text-slate-800">Guru Al-Huda</td>
                                    <td class="px-6 py-4">07:15:20</td>
                                    <td class="px-6 py-4 text-slate-400">-</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Hadir</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-xs text-blue-600 hover:underline">Lihat Foto</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
