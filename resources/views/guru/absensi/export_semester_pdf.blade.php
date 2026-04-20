<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Semester</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 8px; color: #1a1a2e; }
        .header { text-align: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #2563eb; }
        .header h1 { font-size: 14px; font-weight: bold; color: #1e3a8a; margin-bottom: 3px; }
        .header p { font-size: 9px; color: #64748b; }
        .badge-periode { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        thead th {
            background: #1e40af; color: white;
            padding: 5px 3px; text-align: center;
            font-size: 8px; font-weight: bold;
            border: 1px solid #1e3a8a;
        }
        thead th.nama-col { text-align: left; }
        .subheader th {
            background: #dbeafe; color: #1e40af;
            padding: 3px 2px; font-size: 7px;
            border: 1px solid #bfdbfe;
        }
        tbody tr:nth-child(even) { background: #f8faff; }
        tbody td { padding: 4px 3px; border: 1px solid #e2e8f0; text-align: center; font-size: 8px; }
        tbody td.nama { text-align: left; font-weight: 500; }
        .h { color: #16a34a; font-weight: bold; }
        .s { color: #d97706; font-weight: bold; }
        .i { color: #0891b2; font-weight: bold; }
        .a { color: #dc2626; font-weight: bold; }
        .persen-high { color: #16a34a; font-weight: bold; }
        .persen-mid  { color: #d97706; font-weight: bold; }
        .persen-low  { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 16px; font-size: 8px; color: #94a3b8; text-align: right; }
        .ttd { margin-top: 20px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; width: 120px; font-size: 8px; }
        .ttd-box .line { border-bottom: 1px solid #1a1a2e; margin: 35px 0 3px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Absensi Semester</h1>
        <p>{{ $kelas->nama_kelas }} ({{ $kelas->kelas ?? '' }}) &mdash; Hari: {{ $hariList }}</p>
        <p style="margin-top:3px;"><span class="badge-periode">{{ $judulSemester }}</span></p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width:22px;">#</th>
                <th rowspan="2" class="nama-col" style="width:110px;">Nama Siswa</th>
                @foreach($namaBulanList as $nb)
                <th colspan="4">{{ $nb }}</th>
                @endforeach
                <th rowspan="2" style="width:30px;">Total H</th>
                <th rowspan="2" style="width:30px;">Total</th>
                <th rowspan="2" style="width:40px;">% Hadir</th>
            </tr>
            <tr class="subheader">
                @foreach($namaBulanList as $nb)
                <th style="width:16px;" class="h">H</th>
                <th style="width:16px;" class="s">S</th>
                <th style="width:16px;" class="i">I</th>
                <th style="width:16px;" class="a">A</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $i => $r)
            @php
                $pc = $r['persen'] >= 75 ? 'persen-high' : ($r['persen'] >= 50 ? 'persen-mid' : 'persen-low');
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="nama">{{ $r['siswa']->nama }}</td>
                @foreach($namaBulanList as $nb)
                @php $bln = $r['per_bulan'][$nb] ?? ['H'=>0,'S'=>0,'I'=>0,'A'=>0]; @endphp
                <td class="h">{{ $bln['H'] ?: '-' }}</td>
                <td class="s">{{ $bln['S'] ?: '-' }}</td>
                <td class="i">{{ $bln['I'] ?: '-' }}</td>
                <td class="a">{{ $bln['A'] ?: '-' }}</td>
                @endforeach
                <td class="h"><strong>{{ $r['hadir'] }}</strong></td>
                <td><strong>{{ $r['total'] }}</strong></td>
                <td class="{{ $pc }}">{{ $r['persen'] }}%</td>
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
