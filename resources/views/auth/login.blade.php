<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Presensi MA Al-Huda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-linear-to-br from-blue-50 via-slate-50 to-blue-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-blue-100 p-8">
        {{-- Header Logo & Nama Lembaga --}}
        <div class="text-center mb-8">
            <div class="inline-block p-3 rounded-full bg-blue-50 mb-3 border border-blue-100 shadow-sm">
                <img src="{{ asset('image/logo.png') }}" alt="Logo MA Al-Huda" class="w-20 h-20 object-contain mx-auto">
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">MA Al-Huda</h1>
            <p class="text-sm font-medium text-blue-600 mt-0.5">Sistem Presensi Digital</p>
            <p class="text-xs text-slate-400 mt-1">Silakan masuk menggunakan akun Anda</p>
        </div>

        {{-- Pesan Error --}}
        @if ($errors->any())
            <div class="mb-5 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-lg shadow-sm text-sm">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Pesan Status --}}
        @if (session('status'))
            <div class="mb-5 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-r-lg shadow-sm text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form Login --}}
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        placeholder="nama@email.com"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 transition duration-150 outline-none">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-800 transition duration-150 outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-xs text-slate-600">Ingat Saya</span>
                </label>
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-800 active:bg-blue-800 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-blue-600/20 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    Masuk ke Sistem
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} MA Al-Huda. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
