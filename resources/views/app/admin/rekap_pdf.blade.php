<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Absensi - MA Al-Huda</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #059669; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #065f46; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }

        .meta-table, .stats-table, .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .meta-table td { padding: 3px 0; font-size: 11px; }

        .stats-table th, .stats-table td { border: 1px solid #cbd5e1; padding: 6px; text-align: center; }
        .stats-table th { background-color: #f8fafc; font-size: 10px; color: #475569; }

        .data-table th, .data-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        .data-table th { background-color: #f1f5f9; color: #334155; text-transform: uppercase; font-size: 9px; font-weight: bold; }

        .badge { padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 9px; text-align: center; display: inline-block; }
        .badge-hadir { background: #d1fae5; color: #047857; }
        .badge-terlambat { background: #fef3c7; color: #b45309; }
        .badge-izin { background: #dbeafe; color: #1d4ed8; }
        .badge-alpa { background: #ffe4e6; color: #be123c; }

        .footer { margin-top: 25px; text-align: right; font-size: 10px; color: #64748b; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen
        </button>
    </div>

    {{-- Kop Surat --}}
    <div class="header">
        <h2>MA AL-HUDA</h2>
        <p>REKAPITULASI LAPORAN PRESENSI GURU / PEGAWAI</p>
    </div>

    {{-- Information Ringkas --}}
    <table class="meta-table">
        <tr>
            <td width="15%"><strong>Periode Rekap</strong></td>
            <td width="35%">: {{ $periodeText }}</td>
            <td width="15%"><strong>Tanggal Cetak</strong></td>
            <td width="35%">: {{ date('d F Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td><strong>Total Record</strong></td>
            <td>: {{ $attendances->count() }} Data</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ Auth::user()->name ?? 'Administrator' }}</td>
        </tr>
    </table>

    {{-- Ringkasan Statistik --}}
    <table class="stats-table">
        <thead>
            <tr>
                <th>Hadir Tepat Waktu</th>
                <th>Terlambat</th>
                <th>Izin / Sakit</th>
                <th>Tanpa Keterangan (Alpa)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color: #047857; font-weight: bold;">{{ $stats['total_hadir'] }}</td>
                <td style="color: #b45309; font-weight: bold;">{{ $stats['total_terlambat'] }}</td>
                <td style="color: #1d4ed8; font-weight: bold;">{{ $stats['total_izin'] }}</td>
                <td style="color: #be123c; font-weight: bold;">{{ $stats['total_alpa'] }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Tabel Data Rekap --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="15%">Tanggal</th>
                <th>Nama Guru</th>
                <th width="12%">Jam Masuk</th>
                <th width="12%">Jam Pulang</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $row)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ date('d/m/Y', strtotime($row->date)) }}</td>
                    <td>{{ $row->user->name ?? '-' }}</td>
                    <td>{{ $row->time_in ?? '-' }}</td>
                    <td>{{ $row->time_out ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $row->status }}">
                            {{ strtoupper($row->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 15px;">
                        Tidak ada data absensi untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis melalui Sistem Presensi MA Al-Huda</p>
    </div>

</body>
</html>
