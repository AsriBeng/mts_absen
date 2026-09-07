@extends('layouts.layout')

@section('title', 'Pengaturan Hari')
@section('page_title', 'Pengaturan Hari & Jam Kerja')

@section('content')
<div class="space-y-6" x-data="{ showAddModal: false, showEditModal: false, editData: {} }">

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl shadow-sm flex justify-between items-center text-sm">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
        </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-800 text-base">Aturan Jam Kerja & GPS Per Hari</h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Total Data: <span class="font-bold text-emerald-600">{{ $totalCount }} / 7</span>
            </p>
        </div>

        {{-- Tombol Tambah Hari (Di-disabled jika data sudah >= 7) --}}
        @if($totalCount < 7)
            <button @click="showAddModal = true" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Tambah Hari Baru</span>
            </button>
        @else
            <button disabled class="w-full sm:w-auto px-5 py-2.5 bg-slate-200 text-slate-400 font-semibold text-sm rounded-xl cursor-not-allowed flex items-center justify-center gap-2" title="Batas maksimal 7 data sudah tercapai">
                <i class="fas fa-lock"></i>
                <span>Maksimal 7 Data Hari</span>
            </button>
        @endif
    </div>

    {{-- Tabel Absensi Settings --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Nama Hari</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 font-semibold">Jam Masuk / Pulang</th>
                        <th class="px-6 py-3 font-semibold">Toleransi</th>
                        <th class="px-6 py-3 font-semibold">GPS (Lat, Long)</th>
                        <th class="px-6 py-3 font-semibold">Radius</th>
                        <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($settings as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $row->day_name }}</td>
                            <td class="px-6 py-4">
                                @if($row->status === 'masuk')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">
                                        Masuk
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100 uppercase">
                                        Libur
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ date('H:i', strtotime($row->time_in)) }} - {{ date('H:i', strtotime($row->time_out)) }} WIB
                            </td>
                            <td class="px-6 py-4">{{ $row->late_tolerance_minutes }} menit</td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-500">
                                {{ $row->office_latitude }}, {{ $row->office_longitude }}
                            </td>
                            <td class="px-6 py-4">{{ $row->radius_meters }} Meter</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tombol Edit --}}
                                    <button @click="editData = {
                                                id: {{ $row->id }},
                                                day_name: '{{ $row->day_name }}',
                                                status: '{{ $row->status }}',
                                                time_in: '{{ date('H:i', strtotime($row->time_in)) }}',
                                                time_out: '{{ date('H:i', strtotime($row->time_out)) }}',
                                                late_tolerance_minutes: '{{ $row->late_tolerance_minutes }}',
                                                office_latitude: '{{ $row->office_latitude }}',
                                                office_longitude: '{{ $row->office_longitude }}',
                                                radius_meters: '{{ $row->radius_meters }}'
                                            }; showEditModal = true"
                                            class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.days.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data hari ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-sm">
                                Belum ada pengaturan hari yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH HARI --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div x-show="showAddModal" @click="showAddModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showAddModal" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 relative z-10 border border-slate-100">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-slate-800 text-base">Tambah Pengaturan Hari</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form action="{{ route('admin.days.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Hari</label>
                            <input type="text" name="day_name" placeholder="Misal: Senin" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                            <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="masuk">Masuk</option>
                                <option value="libur">Libur</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jam Masuk</label>
                            <input type="time" name="time_in" value="07:00" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jam Pulang</label>
                            <input type="time" name="time_out" value="14:00" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Toleransi (Menit)</label>
                            <input type="number" name="late_tolerance_minutes" value="15" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Radius (Meter)</label>
                            <input type="number" name="radius_meters" value="50" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Latitude Kantor</label>
                            <input type="text" name="office_latitude" placeholder="-7.123456" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Longitude Kantor</label>
                            <input type="text" name="office_longitude" placeholder="112.123456" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm py-2.5 rounded-xl transition">
                            Simpan Hari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT HARI --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showEditModal" class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 relative z-10 border border-slate-100">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-slate-800 text-base">Edit Pengaturan Hari</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form :action="'{{ url('/admin/days') }}/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nama Hari</label>
                            <input type="text" name="day_name" x-model="editData.day_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Status</label>
                            <select name="status" x-model="editData.status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="masuk">Masuk</option>
                                <option value="libur">Libur</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jam Masuk</label>
                            <input type="time" name="time_in" x-model="editData.time_in" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Jam Pulang</label>
                            <input type="time" name="time_out" x-model="editData.time_out" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Toleransi (Menit)</label>
                            <input type="number" name="late_tolerance_minutes" x-model="editData.late_tolerance_minutes" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Radius (Meter)</label>
                            <input type="number" name="radius_meters" x-model="editData.radius_meters" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Latitude Kantor</label>
                            <input type="text" name="office_latitude" x-model="editData.office_latitude" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Longitude Kantor</label>
                            <input type="text" name="office_longitude" x-model="editData.office_longitude" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm py-2.5 rounded-xl transition">
                            Perbarui Pengaturan Hari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
