<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - MA Al-Huda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800 min-h-screen flex flex-col items-center">

    <div class="w-full max-w-md bg-white min-h-screen flex flex-col justify-between shadow-lg">
        {{-- Header Top Bar --}}
        <div>
            <div class="bg-emerald-600 p-6 text-white rounded-b-3xl shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain bg-white/20 p-1 rounded-xl">
                        <div>
                            <h1 class="text-xs text-emerald-100 font-medium">MA Al-Huda</h1>
                            <p class="text-sm font-bold">{{ Auth::user()->name ?? 'Guru Al-Huda' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-white/20 hover:bg-white/30 text-white w-9 h-9 rounded-xl flex items-center justify-center transition">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>

                {{-- Status Hari Ini --}}
                <div class="bg-emerald-700/50 backdrop-blur-sm p-4 rounded-2xl border border-emerald-500/30 text-center mt-2">
                    <p class="text-xs text-emerald-100 uppercase tracking-wider font-semibold">{{ date('l, d F Y') }}</p>
                    <p class="text-2xl font-extrabold text-white mt-1">07:25 <span class="text-xs font-normal">WIB</span></p>
                </div>
            </div>

            {{-- Main Interactive Action --}}
            <div class="p-6 space-y-6">
                {{-- Tombol Utama Absen --}}
                <div class="text-center py-4">
                    <a href="#" class="inline-flex flex-col items-center justify-center w-36 h-36 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-full shadow-xl shadow-emerald-600/30 border-4 border-emerald-100 transition duration-200">
                        <i class="fas fa-qrcode text-3xl mb-1"></i>
                        <span class="text-xs font-bold uppercase tracking-wider">Scan Barcode</span>
                    </a>
                    <p class="text-xs text-slate-400 mt-3">Arahkan kamera ke QR Code papan sekolah</p>
                </div>

                {{-- Status Log Masuk / Pulang --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Absen Masuk</span>
                        <p class="text-lg font-bold text-slate-800 mt-1">07:15:20</p>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-md text-[10px] font-bold">Hadir</span>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center">
                        <span class="text-xs font-semibold text-slate-400 uppercase">Absen Pulang</span>
                        <p class="text-lg font-bold text-slate-400 mt-1">-- : --</p>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-slate-200 text-slate-500 rounded-md text-[10px] font-bold">Belum Absen</span>
                    </div>
                </div>

                {{-- Riwayat Singkat --}}
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-slate-800">Riwayat Terakhir</h3>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Kemarin</p>
                                <p class="text-slate-400">07:10 - 14:00</p>
                            </div>
                        </div>
                        <span class="text-emerald-600 font-semibold bg-emerald-50 px-2 py-1 rounded-md">Hadir</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Footer Mobile --}}
        <div class="border-t border-slate-200 px-6 py-3 bg-white flex justify-around text-slate-400 text-xs font-medium">
            <a href="#" class="flex flex-col items-center gap-1 text-emerald-600">
                <i class="fas fa-home text-base"></i> Beranda
            </a>
            <a href="#" class="flex flex-col items-center gap-1 hover:text-slate-600">
                <i class="fas fa-history text-base"></i> Riwayat
            </a>
            <a href="#" class="flex flex-col items-center gap-1 hover:text-slate-600">
                <i class="fas fa-user text-base"></i> Profil
            </a>
        </div>
    </div>

</body>
</html>
