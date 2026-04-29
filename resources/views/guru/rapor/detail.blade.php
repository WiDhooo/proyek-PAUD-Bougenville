@extends('layouts.guru')

@section('title', 'Analisis dan Rekomendasi Minat Bakat — ' . $siswa->nama)

@section('content')
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('guru.rapor.pilih_kelas') }}">Analisis dan Rekomendasi Minat
                        Bakat</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('guru.rapor.daftar_siswa', $siswa->kelas_id) }}">{{ $siswa->kelas->nama_kelas ?? '-' }}</a>
                </li>
                <li class="breadcrumb-item active">{{ $siswa->nama }}</li>
            </ol>
        </nav>

        {{-- Flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Staleness Warning --}}
        @if($isStale ?? false)
            <div class="alert alert-warning d-flex align-items-center gap-3 rounded-3 py-2 px-3 mb-3" role="alert"
                 style="font-size: 0.875rem; border: 1px solid #f0c040; background: #fffbea;">
                <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:1rem;"></i>
                <div>
                    <strong>Perhatian:</strong> Nilai siswa ini telah diubah setelah analisis terakhir dijalankan.
                    Hasil analisis yang ditampilkan mungkin belum mencerminkan nilai terkini.
                    <a href="{{ route('guru.rapor.daftar_siswa', [$siswa->kelas_id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="alert-link">Jalankan ulang analisis</a> untuk memperbarui.
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 mb-4"
            style="background: linear-gradient(135deg, #0d6efd 0%, #5ab2ff 100%);">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width:56px;height:56px;">
                                <i class="bi bi-person-fill fs-3 text-white"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0">{{ $siswa->nama }}</h3>
                                <p class="mb-0 opacity-75">NIS: {{ $siswa->nis }} | {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </p>
                                <small class="opacity-75">Periode: {{ $periode }} | TA: {{ $tahunAjaran }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('guru.rapor.edit_nilai', ['id' => $siswa->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                            class="btn btn-warning rounded-3 fw-semibold me-1">
                            <i class="bi bi-pencil me-1"></i> Edit Nilai
                        </a>
                        <a href="{{ route('guru.rapor.pdf', ['id' => $siswa->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                            class="btn btn-light rounded-3 fw-semibold">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- PROFIL PERKEMBANGAN — FULL WIDTH DI ATAS --}}
        {{-- ======================================================= --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-graph-up text-primary me-2"></i> Profil Perkembangan
                </h5>
                @if($nilaiPerLingkup->isNotEmpty())
                    <div class="row align-items-center">
                        {{-- Radar Chart --}}
                        <div class="col-lg-7">
                            <canvas id="radarChart" height="320"></canvas>
                        </div>
                        {{-- Ringkasan Per Lingkup --}}
                        <div class="col-lg-5 mt-3 mt-lg-0">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-bar-chart-steps text-primary me-2"></i> Ringkasan Per Lingkup
                            </h6>
                            @foreach ($nilaiPerLingkup as $lingkup => $rataRata)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-truncate me-2" style="max-width: 200px;">{{ $lingkup }}</span>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 120px; height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: {{ ($rataRata / 4) * 100 }}%"></div>
                                        </div>
                                        <span class="fw-bold small" style="min-width:35px;">{{ $rataRata }}/4</span>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Deskripsi Dinamis (dipindah ke sini) --}}
                            @if($smartAnalysis)
                                <div class="mt-3 p-3 rounded-3" style="background: rgba(13,110,253,0.06);">
                                    <h6 class="text-muted small fw-bold text-uppercase mb-1">
                                        <i class="bi bi-lightbulb me-1"></i> Profil Perkembangan
                                    </h6>
                                    <p class="mb-0 small">{{ $smartAnalysis['deskripsi'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-bar-chart fs-1 d-block mb-2"></i>
                        Belum ada data nilai.
                    </div>
                @endif
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- ANALISIS CERDAS AI — FULL WIDTH DI BAWAH PROFIL --}}
        {{-- ======================================================= --}}
        @if($smartAnalysis)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    {{-- Judul + Cluster --}}
                    <div class="d-flex align-items-baseline gap-3 mb-4 pb-3" style="border-bottom: 1px solid #e9ecef;">
                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">Analisis Minat & Bakat</h6>
                        @if($hasilAnalisis)
                            <span class="text-muted small">
                                <i class="bi bi-clock-history me-1"></i> Terakhir dianalisis: {{ $hasilAnalisis->updated_at->format('d M Y, H:i') }} WIB
                            </span>
                        @endif
                        <span class="ms-auto fw-semibold text-dark" style="font-size:0.9rem;">
                            Kelompok {{ $smartAnalysis['cluster_profile']['aspek_dominan'] ?? 'Generalis' }}
                        </span>
                    </div>

                    {{-- Label Individu --}}
                    @if(!empty($smartAnalysis['label_individu']))
                        <p class="text-muted small mb-3">
                            Kekuatan pribadi: <strong>{{ $smartAnalysis['label_individu'] }}</strong>
                            <span class="ms-1 fst-italic">— berbeda tipis dari karakter kelompok</span>
                        </p>
                    @endif

                    {{-- Aspek Kuat & Lemah --}}
                    <div class="row mb-4 g-2">
                        @if(!empty($smartAnalysis['aspek_kuat']))
                            <div class="col-md-{{ empty($smartAnalysis['aspek_lemah']) ? '12' : '6' }}">
                                <p class="text-muted small mb-1 fw-semibold">Aspek kuat</p>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($smartAnalysis['aspek_kuat'] as $lingkup => $avg)
                                        <span class="small px-2 py-1 rounded-2 text-success fw-semibold"
                                            style="background: #f0faf5; border: 1px solid #c3e6cb;">
                                            {{ $lingkup }} <span class="fw-normal text-muted">{{ $avg }}/4</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if(!empty($smartAnalysis['aspek_lemah']))
                            <div class="col-md-{{ empty($smartAnalysis['aspek_kuat']) ? '12' : '6' }}">
                                <p class="text-muted small mb-1 fw-semibold">Perlu perhatian</p>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($smartAnalysis['aspek_lemah'] as $lingkup => $avg)
                                        <span class="small px-2 py-1 rounded-2 text-danger fw-semibold"
                                            style="background: #fff5f5; border: 1px solid #f5c6cb;">
                                            {{ $lingkup }} <span class="fw-normal text-muted">{{ $avg }}/4</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Saran dalam 2 kolom --}}
                    <div class="row g-3">
                        {{-- Kembangkan Kekuatan --}}
                        @if(!empty($smartAnalysis['saran_kekuatan']))
                            <div class="col-md-{{ empty($smartAnalysis['saran_kelemahan']) ? '12' : '6' }}">
                                <div class="p-3 rounded-3 h-100" style="background:#f8fffe; border:1px solid #c3e6cb;">
                                    <p class="text-muted small fw-semibold mb-3" style="border-left:3px solid #198754; padding-left:0.6rem;">Kembangkan Kekuatan</p>
                                    @foreach($smartAnalysis['saran_kekuatan'] as $saran)
                                        <div class="mb-3">
                                            <p class="mb-0 small fw-semibold text-dark">
                                                {{ $saran['lingkup'] }}
                                                <span class="fw-normal text-muted ms-1">Skor {{ $saran['skor'] }}/4</span>
                                            </p>
                                            <p class="mb-0 small text-dark mt-1">{{ $saran['teks'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Stimulasi Kelemahan --}}
                        @if(!empty($smartAnalysis['saran_kelemahan']))
                            <div class="col-md-{{ empty($smartAnalysis['saran_kekuatan']) ? '12' : '6' }}">
                                <div class="p-3 rounded-3 h-100" style="background:#fff8f8; border:1px solid #f5c6cb;">
                                    <p class="text-muted small fw-semibold mb-3" style="border-left:3px solid #dc3545; padding-left:0.6rem;">Stimulasi Aspek yang Perlu Perhatian</p>
                                    @foreach($smartAnalysis['saran_kelemahan'] as $saran)
                                        <div class="mb-3">
                                            <p class="mb-0 small text-dark">
                                                <span class="fw-semibold">{{ $saran['lingkup'] }}</span>
                                                @if($saran['urgent'])
                                                    <span class="ms-1 small text-danger">(Prioritas)</span>
                                                @endif
                                                <span class="text-muted ms-1">Skor {{ $saran['skor'] }}/4</span>
                                            </p>
                                            <p class="mb-0 small text-dark mt-1">{{ $saran['teks'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Saran Generalis --}}
                    @if(!empty($smartAnalysis['saran_generalis']))
                        <div class="mt-3 p-3 rounded-3" style="background:#faf8ff; border:1px solid #d8b4fe;">
                            <p class="small fw-semibold text-muted mb-2" style="border-left:3px solid #7c3aed; padding-left:0.6rem;">Fokus Pengembangan</p>
                            <p class="text-muted small mb-3">
                                Perkembangan murid ini relatif merata. Dua aspek di bawah ini mendapat skor terendah dan direkomendasikan sebagai fokus stimulasi:
                            </p>
                            @foreach($smartAnalysis['saran_generalis'] as $saran)
                                <div class="mb-3">
                                    <p class="mb-0 small text-dark">
                                        <span class="fw-semibold">{{ $saran['lingkup'] }}</span>
                                        <span class="text-muted ms-1">{{ $saran['prioritas'] }} · Skor {{ $saran['skor'] }}/4</span>
                                    </p>
                                    <p class="mb-0 small text-dark mt-1">{{ $saran['teks'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Saran Integratif --}}
                    @if($smartAnalysis['saran_integratif'])
                        <div class="mt-3 p-3 rounded-3" style="background:#f8fbff; border:1px solid #c3d9fb;">
                            <p class="small fw-semibold text-muted mb-1" style="border-left:3px solid #0d6efd; padding-left:0.6rem;">Catatan Integratif</p>
                            <p class="mb-0 small text-dark">{{ $smartAnalysis['saran_integratif'] }}</p>
                        </div>
                    @endif

                    {{-- Red Flags --}}
                    @if(!empty($smartAnalysis['red_flags']))
                        <div class="mt-3 p-3 rounded-3" style="background:#fffdf0; border:1px solid #fde68a;">
                            <p class="small fw-semibold text-dark mb-2" style="border-left:3px solid #d97706; padding-left:0.6rem;">
                                Indikator Belum Berkembang (BB)
                            </p>
                            <div class="small text-dark">
                                @foreach($smartAnalysis['red_flags'] as $flag)
                                    <div class="d-flex align-items-start gap-2 mb-1">
                                        <span class="text-muted mt-1" style="font-size:0.6rem;">●</span>
                                        <span><strong class="text-dark">{{ $flag['lingkup'] }}</strong> — {{ $flag['indikator'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mb-0 mt-2 small text-muted fst-italic">Indikator di atas memerlukan stimulasi segera.</p>
                        </div>
                    @endif

                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">Analisis belum tersedia</h6>
                            <p class="text-muted small mb-3">
                                Rapor murid ini belum memiliki hasil analisis. Analisis perlu dijalankan per kelas agar sistem dapat mengelompokkan dan membandingkan pola perkembangan anak.
                            </p>
                            <div class="mb-3 small text-muted">
                                <strong>Syarat:</strong> minimal 6 siswa dan semua 6 aspek PAUD sudah diinput.
                            </div>
                            <a href="{{ route('guru.rapor.daftar_siswa', [$siswa->kelas_id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                                class="btn btn-sm btn-outline-secondary rounded-3">
                                Kembali ke halaman kelas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif



        {{-- ======================================================= --}}
        {{-- SEKSI BARU: PERBANDINGAN KELOMPOK SEBAYA (Peer Comparison) --}}
        {{-- ======================================================= --}}
        @if(!empty($smartAnalysis['peer_comparison']))
            @php $peer = $smartAnalysis['peer_comparison']; @endphp
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <div class="mb-4 pb-3" style="border-bottom: 1px solid #e9ecef;">
                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">Perbandingan dengan Kelompok Sebaya</h6>
                        <p class="text-muted mb-0 mt-1" style="font-size:0.8rem;">
                            Kelompok <strong>{{ $peer['aspek_dominan_kelompok'] }}</strong>
                            · {{ $peer['jumlah_siswa_kelompok'] }} siswa
                            @php
                                $cohesion = $clusterProfile['cohesion_score'] ?? null;
                                $cLabel = null;
                                if ($cohesion !== null) {
                                    if ($cohesion >= 0.8)     $cLabel = 'sangat kompak';
                                    elseif ($cohesion >= 0.6) $cLabel = 'cukup kompak';
                                    else                      $cLabel = 'beragam';
                                }
                            @endphp
                            @if($cLabel)
                                · Kompakitas: <span class="text-dark fw-semibold">{{ $cLabel }}</span>
                                <span class="text-muted">({{ $cohesion }})</span>
                            @endif
                        </p>
                    </div>

                    {{-- Grid aspek --}}
                    <div class="row g-2">
                        @foreach($peer['detail'] as $lingkup => $cmp)
                            @php
                                $statusColor = match ($cmp['status']) {
                                    'above' => '#146c43',
                                    'below' => '#a71d2a',
                                    default => '#6c757d',
                                };
                                $statusLabel = match ($cmp['status']) {
                                    'above' => 'Di atas',
                                    'below' => 'Di bawah',
                                    default => 'Setara',
                                };
                                $delta = ($cmp['selisih'] > 0 ? '+' : '') . $cmp['selisih'];
                            @endphp
                            <div class="col-md-4 col-sm-6">
                                <div class="p-3 rounded-3 h-100" style="background:#f8f9fa; border:1px solid #e9ecef;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small fw-semibold text-dark">{{ $lingkup }}</span>
                                        <span class="small fw-semibold" style="color:{{ $statusColor }};">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="d-flex align-items-end gap-3">
                                        <div>
                                            <div class="text-muted" style="font-size:0.65rem;">Murid</div>
                                            <div class="fw-bold" style="font-size:1.15rem; color:{{ $statusColor }};">{{ $cmp['skor_anak'] }}</div>
                                        </div>
                                        <div class="text-muted small mb-1">vs</div>
                                        <div>
                                            <div class="text-muted" style="font-size:0.65rem;">Kelompok</div>
                                            <div class="fw-bold text-dark" style="font-size:1.15rem;">{{ $cmp['rata_kelompok'] }}</div>
                                        </div>
                                        <div class="ms-auto text-end">
                                            <div class="text-muted" style="font-size:0.65rem;">Selisih</div>
                                            <div class="fw-semibold small" style="color:{{ $statusColor }}; font-variant-numeric:tabular-nums;">{{ $delta }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif



        {{-- ======================================================= --}}
        {{-- SEKSI BARU: TREN PERKEMBANGAN ANTAR SEMESTER --}}
        {{-- ======================================================= --}}
        @if(!empty($smartAnalysis['trend_data']))
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <div class="mb-4" style="border-left: 3px solid #dee2e6; padding-left: 0.875rem;">
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; letter-spacing: 0.01em;">
                            Tren Perkembangan Antar Semester
                        </h6>
                        <p class="text-muted mb-0 mt-1" style="font-size: 0.8rem;">
                            Perbandingan nilai rata-rata per aspek antara semester lalu dan semester ini.
                        </p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.875rem;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e9ecef;">
                                    <th class="py-2 px-3 text-muted fw-normal" style="font-size:0.75rem; letter-spacing:0.05em; text-transform:uppercase;">Aspek</th>
                                    <th class="py-2 px-3 text-muted fw-normal text-end" style="font-size:0.75rem; letter-spacing:0.05em; text-transform:uppercase;">Lalu</th>
                                    <th class="py-2 px-3 text-muted fw-normal text-end" style="font-size:0.75rem; letter-spacing:0.05em; text-transform:uppercase;">Sekarang</th>
                                    <th class="py-2 px-3 text-muted fw-normal text-end" style="font-size:0.75rem; letter-spacing:0.05em; text-transform:uppercase;">Selisih</th>
                                    <th class="py-2 px-3 text-muted fw-normal" style="font-size:0.75rem; letter-spacing:0.05em; text-transform:uppercase;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($smartAnalysis['trend_data'] as $lingkup => $trend)
                                    @php
                                        $dotColor = match ($trend['trend']) {
                                            'up_significant'   => '#198754',
                                            'up'               => '#0dcaf0',
                                            'stable'           => '#adb5bd',
                                            'down'             => '#fd7e14',
                                            'down_significant' => '#dc3545',
                                            default            => '#adb5bd',
                                        };
                                        $textColor = match ($trend['trend']) {
                                            'up_significant'   => '#146c43',
                                            'up'               => '#087990',
                                            'stable'           => '#6c757d',
                                            'down'             => '#984c0c',
                                            'down_significant' => '#a71d2a',
                                            default            => '#6c757d',
                                        };
                                        $deltaStr = ($trend['delta'] > 0 ? '+' : '') . $trend['delta'];
                                    @endphp
                                    <tr style="border-bottom: 1px solid #f1f3f5;">
                                        <td class="py-2 px-3 fw-semibold text-dark">{{ $lingkup }}</td>
                                        <td class="py-2 px-3 text-end text-muted">{{ $trend['skor_lalu'] }}/4</td>
                                        <td class="py-2 px-3 text-end fw-semibold" style="color: {{ $dotColor }};">{{ $trend['skor_sekarang'] }}/4</td>
                                        <td class="py-2 px-3 text-end fw-semibold" style="color: {{ $textColor }}; font-variant-numeric: tabular-nums;">{{ $deltaStr }}</td>
                                        <td class="py-2 px-3">
                                            <span class="d-flex align-items-center gap-2">
                                                <span style="width:6px; height:6px; border-radius:50%; background:{{ $dotColor }}; display:inline-block; flex-shrink:0;"></span>
                                                <span style="color:{{ $textColor }};">{{ $trend['label'] }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{-- Tabel Detail Nilai --}}
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-table text-primary me-2"></i> Detail Nilai Per Indikator
                </h5>
                <div class="alert alert-info rounded-3 mb-4 border-0" style="background-color: #f8fbff; border-left: 4px solid #0d6efd !important;">
                    <h6 class="fw-bold mb-2 text-primary" style="font-size: 0.9rem;"><i class="bi bi-info-circle me-1"></i> Keterangan Skala Penilaian:</h6>
                    <div class="row g-2 small text-dark">
                        <div class="col-md-3"><strong>BB</strong>: Belum Berkembang (Skor 1)</div>
                        <div class="col-md-3"><strong>MB</strong>: Mulai Berkembang (Skor 2)</div>
                        <div class="col-md-3"><strong>BSH</strong>: Berkembang Sesuai Harapan (Skor 3)</div>
                        <div class="col-md-3"><strong>BSB</strong>: Berkembang Sangat Baik (Skor 4)</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead style="background: linear-gradient(90deg, #0d6efd, #5ab2ff); color: white;">
                            <tr>
                                <th class="py-3 px-3">#</th>
                                <th class="py-3 px-3">Lingkup</th>
                                <th class="py-3 px-3">Sub Lingkup</th>
                                <th class="py-3 px-3">Indikator</th>
                                <th class="py-3 px-3 text-center">Skor</th>
                                <th class="py-3 px-3 text-center">Capaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nilaiRapors as $i => $nr)
                                @php
                                    $labels = [1 => 'BB', 2 => 'MB', 3 => 'BSH', 4 => 'BSB'];
                                    $colors = [1 => 'danger', 2 => 'warning', 3 => 'info', 4 => 'success'];
                                @endphp
                                <tr class="table-row">
                                    <td class="px-3 py-3">{{ $i + 1 }}</td>
                                    <td class="px-3 py-3">{{ $nr->aspekPenilaian->lingkup ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $nr->aspekPenilaian->sub_lingkup ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $nr->aspekPenilaian->indikator ?? '-' }}</td>
                                    <td class="px-3 py-3 text-center fw-bold">{{ $nr->nilai }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="badge bg-{{ $colors[$nr->nilai] ?? 'secondary' }} bg-opacity-10 text-{{ $colors[$nr->nilai] ?? 'secondary' }} border border-{{ $colors[$nr->nilai] ?? 'secondary' }} rounded-pill px-3">
                                            {{ $labels[$nr->nilai] ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada data nilai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <script>
        @if($nilaiPerLingkup->isNotEmpty())
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('radarChart').getContext('2d');
                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: {!! json_encode($nilaiPerLingkup->keys()->toArray()) !!},
                        datasets: [{
                            label: '{{ $siswa->nama }}',
                            data: {!! json_encode($nilaiPerLingkup->values()->toArray()) !!},
                            backgroundColor: 'rgba(13, 110, 253, 0.15)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            r: {
                                beginAtZero: true,
                                min: 0,
                                max: 4,
                                ticks: {
                                    stepSize: 1,
                                    font: { size: 12 },
                                    backdropColor: 'transparent'
                                },
                                pointLabels: {
                                    font: { size: 11, weight: '600' }
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.08)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { font: { size: 13 } }
                            }
                        }
                    }
                });
            });
        @endif
    </script>

    <style>
        .table thead th {
            font-size: 14px;
            font-weight: 600;
            border: none;
        }

        .table-row {
            transition: all 0.2s ease-in-out;
        }

        .table-row:hover {
            background-color: #f5f9ff;
        }
    </style>
@endsection