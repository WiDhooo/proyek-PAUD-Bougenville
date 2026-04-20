<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis dan Rekomendasi Minat Bakat — {{ $siswa->nama }}</title>
    <style>
        @page { margin: 1.5cm; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #0d6efd;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 2px 0;
            color: #0d6efd;
        }
        .header h2 {
            font-size: 14px;
            margin: 0 0 4px 0;
            font-weight: normal;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 16px;
        }
        .info-table td {
            padding: 3px 8px;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        h3 {
            font-size: 13px;
            color: #0d6efd;
            border-bottom: 1px solid #0d6efd;
            padding-bottom: 4px;
            margin: 20px 0 10px 0;
        }
        table.nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10px;
        }
        table.nilai th {
            background: #0d6efd;
            color: white;
            padding: 6px 8px;
            text-align: left;
        }
        table.nilai td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
        }
        table.nilai tr:nth-child(even) {
            background: #f8f9fa;
        }
        .skor-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9px;
        }
        .skor-1 { background: #f8d7da; color: #842029; }
        .skor-2 { background: #fff3cd; color: #664d03; }
        .skor-3 { background: #cff4fc; color: #055160; }
        .skor-4 { background: #d1e7dd; color: #0f5132; }

        .rekomendasi-box {
            border: 2px solid #198754;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 16px;
            background: #f8fff8;
        }
        .rekomendasi-box h4 {
            color: #198754;
            font-size: 12px;
            margin: 0 0 8px 0;
        }
        .rekomendasi-box p {
            margin: 4px 0;
            font-size: 11px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>PAUD Bougenville</h1>
        <h2>RAPOR DIGITAL CERDAS</h2>
        <p>Jl. Kebon Kelapa, Utan Kayu Selatan, Matraman, Jakarta Timur</p>
    </div>

    {{-- INFO SISWA --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Siswa</td>
            <td>: {{ $siswa->nama }}</td>
            <td class="label">NIS</td>
            <td>: {{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
            <td class="label">Jenis Kelamin</td>
            <td>: {{ $siswa->jenis_kelamin }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td>: {{ $periode }}</td>
            <td class="label">Tahun Ajaran</td>
            <td>: {{ $tahunAjaran }}</td>
        </tr>
    </table>

    {{-- RINGKASAN PER LINGKUP --}}
    <h3>📊 Ringkasan Perkembangan Per Lingkup</h3>
    <table class="nilai">
        <thead>
            <tr>
                <th width="60%">Lingkup Perkembangan</th>
                <th width="20%" style="text-align:center;">Rata-rata</th>
                <th width="20%" style="text-align:center;">Capaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiPerLingkup as $lingkup => $rataRata)
            @php
                if ($rataRata >= 3.5) $badge = 'BSB';
                elseif ($rataRata >= 2.5) $badge = 'BSH';
                elseif ($rataRata >= 1.5) $badge = 'MB';
                else $badge = 'BB';
                
                $skorClass = $rataRata >= 3.5 ? 4 : ($rataRata >= 2.5 ? 3 : ($rataRata >= 1.5 ? 2 : 1));
            @endphp
            <tr>
                <td>{{ $lingkup }}</td>
                <td style="text-align:center; font-weight:bold;">{{ $rataRata }}</td>
                <td style="text-align:center;">
                    <span class="skor-badge skor-{{ $skorClass }}">{{ $badge }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- DETAIL NILAI --}}
    <h3>📝 Detail Nilai Per Indikator</h3>
    <table class="nilai">
        <thead>
            <tr>
                <th>#</th>
                <th>Lingkup</th>
                <th>Sub Lingkup</th>
                <th>Indikator</th>
                <th style="text-align:center;">Skor</th>
                <th style="text-align:center;">Capaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiRapors as $i => $nr)
            @php
                $labels = [1 => 'BB', 2 => 'MB', 3 => 'BSH', 4 => 'BSB'];
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $nr->aspekPenilaian->lingkup ?? '-' }}</td>
                <td>{{ $nr->aspekPenilaian->sub_lingkup ?? '-' }}</td>
                <td>{{ $nr->aspekPenilaian->indikator ?? '-' }}</td>
                <td style="text-align:center; font-weight:bold;">{{ $nr->nilai }}</td>
                <td style="text-align:center;">
                    <span class="skor-badge skor-{{ $nr->nilai }}">{{ $labels[$nr->nilai] ?? '-' }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ANALISIS CERDAS AI --}}
    @if($smartAnalysis)
    <div class="rekomendasi-box">
        <h4>🤖 Analisis Cerdas AI — {{ $smartAnalysis['label_utama'] }}</h4>

        @if(!empty($smartAnalysis['aspek_kuat']))
        <p><strong>Aspek Kuat:</strong>
            @foreach($smartAnalysis['aspek_kuat'] as $lingkup => $avg)
                {{ $lingkup }} ({{ $avg }}){{ !$loop->last ? ', ' : '' }}
            @endforeach
        </p>
        @endif

        @if(!empty($smartAnalysis['aspek_lemah']))
        <p><strong>Perlu Perhatian:</strong>
            @foreach($smartAnalysis['aspek_lemah'] as $lingkup => $avg)
                {{ $lingkup }} ({{ $avg }}){{ !$loop->last ? ', ' : '' }}
            @endforeach
        </p>
        @endif

        <p><strong>Profil Perkembangan:</strong> {{ $smartAnalysis['deskripsi'] }}</p>

        @if(!empty($smartAnalysis['saran_kekuatan']))
        <p><strong>✅ Kembangkan Kekuatan:</strong></p>
        @foreach($smartAnalysis['saran_kekuatan'] as $saran)
            <p style="margin-left:12px;">• {{ $saran }}</p>
        @endforeach
        @endif

        @if(!empty($smartAnalysis['saran_kelemahan']))
        <p><strong>📌 Stimulasi Aspek Lemah:</strong></p>
        @foreach($smartAnalysis['saran_kelemahan'] as $saran)
            <p style="margin-left:12px;">• {{ $saran }}</p>
        @endforeach
        @endif

        @if($smartAnalysis['saran_integratif'])
        <p><strong>💡 Saran Integratif:</strong> {{ $smartAnalysis['saran_integratif'] }}</p>
        @endif

        @if(!empty($smartAnalysis['red_flags']))
        <p style="color:#842029; margin-top:8px;"><strong>⚠️ Peringatan Indikator Kritis (Skor 1):</strong></p>
        @foreach($smartAnalysis['red_flags'] as $flag)
            <p style="margin-left:12px; color:#842029;">• {{ $flag['lingkup'] }} — {{ $flag['indikator'] }} [BB]</p>
        @endforeach
        <p style="font-style:italic; font-size:10px; color:#666;">Indikator di atas menunjukkan anak Belum Berkembang dan memerlukan stimulasi mendesak.</p>
        @endif
    </div>
    @endif

    {{-- TANDA TANGAN --}}
    <table style="width:100%; margin-top:40px;">
        <tr>
            <td style="width:50%; text-align:center;">
                Orang Tua / Wali
                <br><br><br><br>
                ( ........................... )
            </td>
            <td style="width:50%; text-align:center;">
                Jakarta, {{ now()->translatedFormat('d F Y') }}
                <br>Guru Kelas
                <br><br><br><br>
                ( ........................... )
            </td>
        </tr>
    </table>

    <div class="footer">
        Rapor ini digenerate secara otomatis oleh Sistem Analisis dan Rekomendasi Minat Bakat PAUD Bougenville
    </div>

</body>
</html>
