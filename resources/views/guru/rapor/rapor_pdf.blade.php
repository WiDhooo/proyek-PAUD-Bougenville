<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Analisis dan Rekomendasi Minat Bakat — {{ $siswa->nama }}</title>
    <style>
        /* ===== PAGE SETUP ===== */
        @page {
            margin: 1.8cm 1.5cm 2cm 1.5cm;
        }

        /* ===== BASE ===== */
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5px;
            color: #2d2d2d;
            line-height: 1.55;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 3px 0;
            color: #1a1a1a;
            letter-spacing: 0.3px;
        }

        .header h2 {
            font-size: 11.5px;
            margin: 0 0 4px 0;
            font-weight: normal;
            color: #555;
        }

        .header p {
            margin: 0;
            font-size: 9.5px;
            color: #888;
        }

        /* ===== INFO TABLE ===== */
        .info-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            width: 100px;
            color: #555;
        }

        /* ===== SECTION HEADING ===== */
        h3 {
            font-size: 11.5px;
            color: #2d2d2d;
            border-bottom: 1.5px solid #dee2e6;
            padding-bottom: 4px;
            margin: 18px 0 8px 0;
            page-break-after: avoid;
        }

        /* ===== TABLES ===== */
        table.nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5px;
        }

        table.nilai thead {
            display: table-header-group;
        }

        table.nilai th {
            background: #f1f3f5;
            color: #444;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1.5px solid #dee2e6;
        }

        table.nilai td {
            padding: 5px 8px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: top;
        }

        table.nilai tr:nth-child(even) td {
            background: #fafafa;
        }

        table.nilai tr {
            page-break-inside: avoid;
        }

        /* ===== BADGES ===== */
        .skor-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }

        .skor-1 { background: #f8d7da; color: #842029; }
        .skor-2 { background: #fff3cd; color: #664d03; }
        .skor-3 { background: #cff4fc; color: #055160; }
        .skor-4 { background: #d1e7dd; color: #0f5132; }

        /* ===== CONTENT BOXES ===== */
        .box {
            border-radius: 5px;
            padding: 10px 13px;
            margin-top: 10px;
            page-break-inside: avoid;
            border: 1px solid #dee2e6;
            background: #fafafa;
        }

        .box h4 {
            font-size: 10.5px;
            margin: 0 0 7px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e9ecef;
            color: #2d2d2d;
        }

        .box p {
            margin: 4px 0;
            font-size: 10px;
        }

        /* ===== SARAN ITEM ===== */
        .saran-item {
            margin-bottom: 8px;
            padding: 7px 10px;
            border-left: 3px solid #dee2e6;
            background: #fafafa;
            page-break-inside: avoid;
        }

        .saran-item.kekuatan {
            border-left-color: #198754;
            background: #f8fffe;
        }

        .saran-item.kelemahan {
            border-left-color: #dc3545;
            background: #fff8f8;
        }

        /* ===== STATUS COLORS ===== */
        .status-above { color: #146c43; font-weight: bold; }
        .status-below { color: #a71d2a; font-weight: bold; }
        .status-equal { color: #6c757d; }

        /* ===== TREND COLORS ===== */
        .trend-up-significant { color: #146c43; font-weight: bold; }
        .trend-up             { color: #087990; }
        .trend-stable         { color: #6c757d; }
        .trend-down           { color: #984c0c; }
        .trend-down-significant { color: #a71d2a; font-weight: bold; }

        /* ===== RED FLAGS ===== */
        .red-flag-box {
            margin-top: 10px;
            padding: 9px 12px;
            border: 1.5px solid #fde68a;
            border-radius: 5px;
            background: #fffdf0;
            page-break-inside: avoid;
        }

        .red-flag-title {
            font-size: 10px;
            font-weight: bold;
            color: #92400e;
            margin: 0 0 5px 0;
        }

        .red-flag-item {
            margin-left: 10px;
            color: #92400e;
            margin-bottom: 2px;
            font-size: 9.5px;
        }

        /* ===== FOOTER (fixed, per page) ===== */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8.5px;
            color: #aaa;
            border-top: 1px solid #e0e0e0;
            padding-top: 5px;
        }

        /* ===== PAGE BREAK HELPERS ===== */
        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* ===== KETERANGAN BOX ===== */
        .keterangan-box {
            background: #f8f9fa;
            border-left: 3px solid #dee2e6;
            padding: 7px 11px;
            margin-bottom: 10px;
            font-size: 9.5px;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    {{-- FOOTER (fixed per page) --}}
    <div class="footer">
        Rapor ini digenerate secara otomatis oleh Sistem Analisis dan Rekomendasi Minat Bakat PAUD Bougenville
    </div>

    {{-- HEADER --}}
    <div class="header">
        <h1>PAUD Bougenville</h1>
        <h2>Analisis &amp; Rekomendasi Minat Bakat</h2>
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
    <h3>Ringkasan Perkembangan Per Lingkup</h3>
    <table class="nilai">
        <thead>
            <tr>
                <th width="45%">Lingkup Perkembangan</th>
                <th width="20%" style="text-align:center;">Rata-rata</th>
                <th width="15%" style="text-align:center;">Capaian</th>
                @if(!empty($smartAnalysis['trend_data']))
                    <th width="20%" style="text-align:center;">Tren vs Semester Lalu</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiPerLingkup as $lingkup => $rataRata)
                @php
                    if ($rataRata >= 3.5)      { $badge = 'BSB'; $sc = 4; }
                    elseif ($rataRata >= 2.5)  { $badge = 'BSH'; $sc = 3; }
                    elseif ($rataRata >= 1.5)  { $badge = 'MB';  $sc = 2; }
                    else                        { $badge = 'BB';  $sc = 1; }
                    $trendInfo = $smartAnalysis['trend_data'][$lingkup] ?? null;
                @endphp
                <tr>
                    <td>{{ $lingkup }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $rataRata }}</td>
                    <td style="text-align:center;">
                        <span class="skor-badge skor-{{ $sc }}">{{ $badge }}</span>
                    </td>
                    @if(!empty($smartAnalysis['trend_data']))
                        <td style="text-align:center;">
                            @if($trendInfo)
                                @php
                                    $cssClass = 'trend-' . str_replace('_', '-', $trendInfo['trend']);
                                    $deltaStr = ($trendInfo['delta'] > 0 ? '+' : '') . $trendInfo['delta'];
                                @endphp
                                <span class="{{ $cssClass }}">{{ $deltaStr }} &nbsp;{{ $trendInfo['label'] }}</span>
                            @else
                                <span style="color:#aaa;">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ANALISIS MINAT & BAKAT --}}
    @if($smartAnalysis)
        <div class="box">
            <h4>Analisis Minat &amp; Bakat</h4>

            <p>
                <strong>Profil:</strong> {{ $smartAnalysis['label_utama'] }}
                @if(!empty($smartAnalysis['label_individu']))
                    &nbsp;|&nbsp; <em>Kekuatan Pribadi: {{ $smartAnalysis['label_individu'] }}</em>
                @endif
            </p>

            @if(!empty($smartAnalysis['aspek_kuat']))
                <p><strong>Aspek kuat:</strong>
                    @foreach($smartAnalysis['aspek_kuat'] as $lingkup => $avg)
                        {{ $lingkup }} ({{ $avg }}/4){{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </p>
            @endif

            @if(!empty($smartAnalysis['aspek_lemah']))
                <p><strong>Perlu perhatian:</strong>
                    @foreach($smartAnalysis['aspek_lemah'] as $lingkup => $avg)
                        {{ $lingkup }} ({{ $avg }}/4){{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </p>
            @endif

            <p><strong>Profil Perkembangan:</strong> {{ $smartAnalysis['deskripsi'] }}</p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         HALAMAN 2 — REKOMENDASI DETAIL
    ═══════════════════════════════════════════════════════ --}}
    <div class="page-break"></div>

    {{-- REKOMENDASI KEKUATAN --}}
    @if(!empty($smartAnalysis['saran_kekuatan']))
        <h3>Kembangkan Kekuatan</h3>
        @foreach($smartAnalysis['saran_kekuatan'] as $saran)
            <div class="saran-item kekuatan">
                <strong>{{ $saran['lingkup'] }}</strong>
                <span style="color:#555;"> — Skor {{ $saran['skor'] }}/4</span><br>
                {{ $saran['teks'] }}
            </div>
        @endforeach
    @endif

    {{-- REKOMENDASI STIMULASI --}}
    @if(!empty($smartAnalysis['saran_kelemahan']))
        <h3>Stimulasi Aspek yang Perlu Perhatian</h3>
        @foreach($smartAnalysis['saran_kelemahan'] as $saran)
            <div class="saran-item kelemahan">
                <strong>{{ $saran['lingkup'] }}</strong>
                @if($saran['urgent'])
                    <span style="color:#a71d2a;"> (Prioritas)</span>
                @endif
                <span style="color:#555;"> — Skor {{ $saran['skor'] }}/4</span><br>
                {{ $saran['teks'] }}
            </div>
        @endforeach
    @endif

    {{-- SARAN GENERALIS --}}
    @if(!empty($smartAnalysis['saran_generalis']))
        <h3>Fokus Pengembangan</h3>
        <p style="font-size:9.5px; color:#555; margin-bottom:8px;">
            Perkembangan murid ini relatif merata. Dua aspek di bawah ini mendapat skor terendah dan direkomendasikan sebagai fokus stimulasi:
        </p>
        @foreach($smartAnalysis['saran_generalis'] as $saran)
            <div class="saran-item">
                <strong>{{ $saran['lingkup'] }}</strong>
                <span style="color:#555;"> — {{ $saran['prioritas'] }} · Skor {{ $saran['skor'] }}/4</span><br>
                {{ $saran['teks'] }}
            </div>
        @endforeach
    @endif

    {{-- CATATAN INTEGRATIF --}}
    @if(!empty($smartAnalysis['saran_integratif']))
        <div class="no-break" style="margin-top: 10px; padding: 8px 12px; border-left: 3px solid #dee2e6; background: #f8fbff; border-radius: 4px;">
            <strong style="color: #444;">Catatan Integratif:</strong>
            {{ $smartAnalysis['saran_integratif'] }}
        </div>
    @endif

    {{-- RED FLAGS --}}
    @if(!empty($smartAnalysis['red_flags']))
        <div class="red-flag-box">
            <p class="red-flag-title">Indikator Belum Berkembang (BB)</p>
            @foreach($smartAnalysis['red_flags'] as $flag)
                <p class="red-flag-item">&#8226; <strong>{{ $flag['lingkup'] }}</strong> — {{ $flag['indikator'] }}</p>
            @endforeach
            <p style="font-style:italic; font-size:9px; color:#888; margin-top:5px; margin-bottom:0;">
                Indikator di atas memerlukan stimulasi segera.
            </p>
        </div>
    @endif

    {{-- PERBANDINGAN KELOMPOK SEBAYA --}}
    @if(!empty($smartAnalysis['peer_comparison']))
        @php $peer = $smartAnalysis['peer_comparison']; @endphp
        <h3>Perbandingan dengan Kelompok Sebaya</h3>
        <div class="box">
            <h4>
                Kelompok {{ $peer['jumlah_siswa_kelompok'] }} Siswa &mdash; {{ $peer['aspek_dominan_kelompok'] }}
                @php
                    $cohesion = $clusterProfile['cohesion_score'] ?? null;
                    if ($cohesion !== null) {
                        if ($cohesion >= 0.8)     $cLabel = 'sangat kompak';
                        elseif ($cohesion >= 0.6) $cLabel = 'cukup kompak';
                        else                      $cLabel = 'beragam';
                    } else { $cLabel = null; }
                @endphp
                @if($cLabel)
                    &nbsp;&middot;&nbsp; Kompakitas: <em>{{ $cLabel }}</em> ({{ $cohesion }})
                @endif
            </h4>
            <table class="nilai" style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th>Aspek</th>
                        <th style="text-align:center;">Skor Murid</th>
                        <th style="text-align:center;">Rata-rata Kelompok</th>
                        <th style="text-align:center;">Selisih</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peer['detail'] as $lingkup => $cmp)
                        @php
                            $statusLabel = match($cmp['status']) {
                                'above' => 'Di atas',
                                'below' => 'Di bawah',
                                default => 'Setara',
                            };
                            $deltaStr = ($cmp['selisih'] > 0 ? '+' : '') . $cmp['selisih'];
                        @endphp
                        <tr>
                            <td>{{ $lingkup }}</td>
                            <td style="text-align:center; font-weight:bold;" class="status-{{ $cmp['status'] }}">{{ $cmp['skor_anak'] }}</td>
                            <td style="text-align:center;">{{ $cmp['rata_kelompok'] }}</td>
                            <td style="text-align:center;" class="status-{{ $cmp['status'] }}">{{ $deltaStr }}</td>
                            <td class="status-{{ $cmp['status'] }}">{{ $statusLabel }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- TREN PERKEMBANGAN --}}
    @if(!empty($smartAnalysis['trend_data']))
        <h3>Tren Perkembangan Antar Semester</h3>
        <table class="nilai">
            <thead>
                <tr>
                    <th>Aspek</th>
                    <th style="text-align:center;">Semester Lalu</th>
                    <th style="text-align:center;">Semester Ini</th>
                    <th style="text-align:center;">Selisih</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($smartAnalysis['trend_data'] as $lingkup => $trend)
                    @php
                        $cssClass = 'trend-' . str_replace('_', '-', $trend['trend']);
                        $deltaStr = ($trend['delta'] > 0 ? '+' : '') . $trend['delta'];
                    @endphp
                    <tr>
                        <td>{{ $lingkup }}</td>
                        <td style="text-align:center;">{{ $trend['skor_lalu'] }}</td>
                        <td style="text-align:center; font-weight:bold;" class="{{ $cssClass }}">{{ $trend['skor_sekarang'] }}</td>
                        <td style="text-align:center; font-variant-numeric: tabular-nums;" class="{{ $cssClass }}">{{ $deltaStr }}</td>
                        <td class="{{ $cssClass }}">{{ $trend['label'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- HALAMAN 3 — DETAIL NILAI PER INDIKATOR --}}
    <div class="page-break"></div>

    <h3>Detail Nilai Per Indikator</h3>
    <div class="keterangan-box">
        <strong>Keterangan Skala Penilaian:</strong>&nbsp;
        <strong>BB</strong>: Belum Berkembang (1) &nbsp;|&nbsp;
        <strong>MB</strong>: Mulai Berkembang (2) &nbsp;|&nbsp;
        <strong>BSH</strong>: Berkembang Sesuai Harapan (3) &nbsp;|&nbsp;
        <strong>BSB</strong>: Berkembang Sangat Baik (4)
    </div>

    <table class="nilai">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="14%">Lingkup</th>
                <th width="18%">Sub Lingkup</th>
                <th width="50%">Indikator</th>
                <th width="7%" style="text-align:center;">Skor</th>
                <th width="8%" style="text-align:center;">Capaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiRapors as $i => $nr)
                @php $labels = [1 => 'BB', 2 => 'MB', 3 => 'BSH', 4 => 'BSB']; @endphp
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



</body>

</html>