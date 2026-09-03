@extends('layouts.layout')

@section('title', 'Absen Saya')
@section('page_title', 'Rekap Presensi Saya')

@section('content')
<div class="space-y-6" x-data="{ type: '{{ $filterType }}' }">

    {{-- Filter Periode Rekap --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('guru.absen_saya') }}" method="GET" class="flex flex-col lg:flex-row items-end justify-between gap-4">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full lg:w-auto">

                {{-- Select Tipe Filter --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Periode Rekap</label>
                    <select name="filter_type" x-model="type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                    </select>
                </div>

                {{-- Input Tanggal Mulai (Khusus Mingguan) --}}
                <div x-show="type === 'mingguan'">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Mulai (7 Hari)</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                {{-- Select Bulan (Khusus Bulanan) --}}
                <div x-show="type === 'bulanan'">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Tahun (Bulanan & Tahunan) --}}
                <div x-show="type === 'bulanan' || type === 'tahunan'">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach(range(date('Y'), date('Y') - 4) as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Tombol Filter & Cetak Export --}}
            <div class="flex items-center gap-2 w-full lg:w-auto">
                <button type="submit" class="flex-1 lg:flex-none px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>

                {{-- Tombol Cetak / Export --}}
                <a href="{{ route('guru.absen_saya.export_pdf', request()->all()) }}" target="_blank"
                   class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fas fa-print"></i>
                    <span class="hidden sm:inline">Cetak / Export</span>
                </a>
            </div>

        </form>
    </div>

    {{-- Ringkasan Statistik Presensi Saya --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs text-slate-400 uppercase font-semibold">Hadir Tepat Waktu</span>
            <p class="text-xl font-bold text-emerald-600 mt-1">{{ $stats['total_hadir'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs text-slate-400 uppercase font-semibold">Terlambat</span>
            <p class="text-xl font-bold text-amber-500 mt-1">{{ $stats['total_terlambat'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs text-slate-400 uppercase font-semibold">Izin / Sakit</span>
            <p class="text-xl font-bold text-blue-500 mt-1">{{ $stats['total_izin'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs text-slate-400 uppercase font-semibold">Tanpa Keterangan</span>
            <p class="text-xl font-bold text-rose-500 mt-1">{{ $stats['total_alpa'] }}</p>
        </div>
    </div>

    {{-- Tabel Riwayat Absen Saya --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Riwayat Presensi Pribadi</h3>
                <p class="text-xs text-slate-400 mt-0.5">Periode: <span class="font-semibold text-emerald-600">{{ $periodeText }}</span></p>
            </div>
            <span class="text-xs bg-emerald-50 text-emerald-700 font-bold px-3 py-1 rounded-full border border-emerald-100">
                Total Kehadiran: {{ $attendances->count() }} Hari
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Tanggal</th>
                        <th class="px-6 py-3 font-semibold">Jam Masuk</th>
                        <th class="px-6 py-3 font-semibold">Jam Pulang</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ date('d/m/Y', strtotime($row->date)) }}</td>
                            <td class="px-6 py-4">{{ $row->time_in ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $row->time_out ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($row->status === 'hadir')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">Hadir</span>
                                @elseif($row->status === 'terlambat')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">Terlambat</span>
                                @elseif($row->status === 'izin')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600">Izin</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600">Alpa</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-sm">
                                Tidak ada data presensi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
