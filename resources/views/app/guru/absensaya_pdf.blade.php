<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi - {{ $user->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 20px; }
        .header { text-align: center; border-b: 2px solid #059669; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #065f46; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 11px; color: #666; }
        .info-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        .data-table th { background-color: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 10px; }
        .badge { padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .badge-hadir { background: #d1fae5; color: #047857; }
        .badge-terlambat { background: #fef3c7; color: #b45309; }
        .badge-izin { background: #dbeafe; color: #1d4ed8; }
        .badge-alpa { background: #ffe4e6; color: #be123c; }
        .footer { margin-top: 30px; text-align: right; font-size: 11px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Cetak Dokumen
        </button>
    </div>

    <div class="header">
        <h2>MA AL-HUDA</h2>
        <p>LAPORAN PRESENSI PRIBADI GURU</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Guru</strong></td>
            <td width="35%">: {{ $user->name }}</td>
            <td width="15%"><strong>Periode</strong></td>
            <td width="35%">: {{ $periodeText }}</td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td>: {{ $user->email }}</td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d F Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ date('d/m/Y', strtotime($row->date)) }}</td>
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
                    <td colspan="5" style="text-align: center; color: #94a3b8;">
                        Tidak ada data presensi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak Otomatis oleh Sistem Presensi MA Al-Huda</p>
    </div>

</body>
</html>
