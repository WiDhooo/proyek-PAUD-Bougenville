<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Rekap Absensi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1a1a2e; }
        .header { text-align: center; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #2563eb; }
        .header h1 { font-size: 16px; font-weight: bold; color: #1e3a8a; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #64748b; }
        .badge-periode { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th {
            background: #1e40af;
            color: white;
            padding: 7px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #1e3a8a;
        }
        thead th:nth-child(2) { text-align: left; }
        tbody tr:nth-child(even) { background: #f0f9ff; }
        tbody td { padding: 6px; border: 1px solid #e2e8f0; text-align: center; }
        tbody td:nth-child(2) { text-align: left; font-weight: 500; }
        .h { color: #16a34a; font-weight: bold; }
        .s { color: #d97706; font-weight: bold; }
        .i { color: #0891b2; font-weight: bold; }
        .a { color: #dc2626; font-weight: bold; }
        .persen-high { color: #16a34a; font-weight: bold; }
        .persen-mid { color: #d97706; font-weight: bold; }
        .persen-low { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
        .progress-bar { display: inline-block; height: 4px; border-radius: 2px; vertical-align: middle; }
        .ttd { margin-top: 30px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; width: 140px; }
        .ttd-box .line { border-bottom: 1px solid #1a1a2e; margin: 40px 0 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Absensi Siswa</h1>
        <p>{{ $jadwal->kelas->nama_kelas }} ({{ $jadwal->kelas->kelas ?? '' }}) &mdash; Setiap {{ $jadwal->hari }}</p>
        <p style="margin-top:4px;">{{ $namaBulan }} &nbsp; <span class="badge-periode">{{ $periode }} {{ $tahunAjaran }}</span></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:28px;">No</th>
                <th style="width:140px; text-align:left;">Nama Siswa</th>
                <th style="width:32px;">H</th>
                <th style="width:32px;">S</th>
                <th style="width:32px;">I</th>
                <th style="width:32px;">A</th>
                <th style="width:36px;">Total</th>
                <th style="width:60px;">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $i => $r)
            @php
                $persenClass = $r['persen'] >= 75 ? 'persen-high' : ($r['persen'] >= 50 ? 'persen-mid' : 'persen-low');
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r['siswa']->nama }}</td>
                <td class="h">{{ $r['hadir'] }}</td>
                <td class="s">{{ $r['sakit'] }}</td>
                <td class="i">{{ $r['izin'] }}</td>
                <td class="a">{{ $r['alpha'] }}</td>
                <td><strong>{{ $r['total'] }}</strong></td>
                <td class="{{ $persenClass }}">{{ $r['persen'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd">
        <div class="ttd-box">
            <p>Guru Kelas,</p>
            <div class="line"></div>
            <p>{{ auth()->user()->name ?? '...' }}</p>
        </div>
    </div>

    <div class="footer">
        Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
    </div>
</body>
</html>
