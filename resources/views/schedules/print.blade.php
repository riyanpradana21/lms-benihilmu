<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        * { box-sizing: border-box; }
        body { color: #172033; font: 12px Arial, sans-serif; }
        header { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 14px; margin-bottom: 18px; }
        h1 { margin: 0 0 5px; color: #172554; font-size: 21px; }
        p { margin: 4px 0; color: #475569; }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; background: #eff6ff; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { color: #1e3a8a; font-size: 10px; text-transform: uppercase; }
        tr { page-break-inside: avoid; }
        .actions { margin-bottom: 14px; }
        button { border: 0; border-radius: 6px; background: #1e3a8a; color: white; padding: 9px 14px; cursor: pointer; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="actions"><button onclick="window.print()">Cetak / Simpan PDF</button></div>
    <header>
        <h1>{{ config('app.name') }}</h1>
        <p>{{ $title }}{{ $class ? ' — '.$class->name : '' }}</p>
        <p>{{ $activeYear?->name ?? 'Tahun ajaran aktif' }} · Dicetak {{ now()->translatedFormat('d F Y') }}</p>
    </header>
    <table>
        <thead><tr><th>Hari</th><th>Waktu</th><th>Kelas</th><th>Mata Pelajaran</th><th>Guru</th><th>Ruang</th></tr></thead>
        <tbody>
        @forelse($schedules as $schedule)
            <tr>
                <td>{{ ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu'][$schedule->day_of_week] ?? ucfirst($schedule->day_of_week) }}</td>
                <td>{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</td>
                <td>{{ $schedule->schoolClass?->name }}</td>
                <td>{{ $schedule->subject?->name }}</td>
                <td>{{ $schedule->teacher?->user?->name }}</td>
                <td>{{ $schedule->room ?: '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6">Belum ada jadwal untuk dicetak.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
