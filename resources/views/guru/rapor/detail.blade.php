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

        {{-- Header Card --}}
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
            <div class="card border-0 shadow-sm rounded-4 mb-4"
                style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-robot text-success me-2"></i> Analisis Cerdas AI
                    </h5>

                    {{-- Label + Cluster --}}
                    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                        @if($hasilAnalisis)
                            <span class="badge bg-success rounded-pill px-3 py-2 fs-6">
                                Cluster {{ $hasilAnalisis->cluster_group }}
                            </span>
                        @endif
                        <h6 class="fw-bold text-success mb-0">{{ $smartAnalysis['label_utama'] }}</h6>
                    </div>

                    {{-- Aspek Kuat & Lemah Badges --}}
                    <div class="row mb-3">
                        @if(!empty($smartAnalysis['aspek_kuat']))
                            <div class="col-md-{{ empty($smartAnalysis['aspek_lemah']) ? '12' : '6' }} mb-2 mb-md-0">
                                <small class="text-muted fw-bold text-uppercase"><i class="bi bi-star-fill text-warning me-1"></i>
                                    Aspek Kuat:</small>
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @foreach($smartAnalysis['aspek_kuat'] as $lingkup => $avg)
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">
                                            {{ $lingkup }} — {{ $avg }}/4.0
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if(!empty($smartAnalysis['aspek_lemah']))
                            <div class="col-md-{{ empty($smartAnalysis['aspek_kuat']) ? '12' : '6' }}">
                                <small class="text-muted fw-bold text-uppercase"><i
                                        class="bi bi-exclamation-triangle text-danger me-1"></i> Perlu Perhatian:</small>
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @foreach($smartAnalysis['aspek_lemah'] as $lingkup => $avg)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2">
                                            {{ $lingkup }} — {{ $avg }}/4.0
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Saran dalam 2 kolom --}}
                    <div class="row g-3">
                        {{-- Saran Kekuatan Personal --}}
                        @if(!empty($smartAnalysis['saran_kekuatan']))
                            <div class="col-md-{{ empty($smartAnalysis['saran_kelemahan']) ? '12' : '6' }}">
                                <div class="p-3 rounded-3 h-100"
                                    style="background: rgba(25,135,84,0.08); border-left: 4px solid #198754;">
                                    <h6 class="text-success small fw-bold text-uppercase mb-3">
                                        <i class="bi bi-hand-thumbs-up me-1"></i> Kembangkan Kekuatan
                                    </h6>
                                    @foreach($smartAnalysis['saran_kekuatan'] as $saran)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 me-2 small">{{ $saran['lingkup'] }}</span>
                                                <small class="text-muted">Skor {{ $saran['skor'] }}/4</small>
                                            </div>
                                            <p class="mb-0 small text-dark">{{ $saran['teks'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Saran Kelemahan Personal --}}
                        @if(!empty($smartAnalysis['saran_kelemahan']))
                            <div class="col-md-{{ empty($smartAnalysis['saran_kekuatan']) ? '12' : '6' }}">
                                <div class="p-3 rounded-3 h-100"
                                    style="background: rgba(220,53,69,0.08); border-left: 4px solid #dc3545;">
                                    <h6 class="text-danger small fw-bold text-uppercase mb-3">
                                        <i class="bi bi-arrow-up-circle me-1"></i> Stimulasi Aspek yang Perlu Perhatian
                                    </h6>
                                    @foreach($smartAnalysis['saran_kelemahan'] as $saran)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge {{ $saran['urgent'] ? 'bg-danger' : 'bg-warning bg-opacity-10 text-dark' }} rounded-pill px-2 py-1 me-2 small">
                                                    {{ $saran['urgent'] ? 'Prioritas' : '' }} {{ $saran['lingkup'] }}
                                                </span>
                                                <small class="text-muted">Skor {{ $saran['skor'] }}/4</small>
                                            </div>
                                            <p class="mb-0 small text-dark">{{ $saran['teks'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Saran Integratif --}}
                    @if($smartAnalysis['saran_integratif'])
                        <div class="p-3 rounded-3 mt-3" style="background: rgba(13,110,253,0.08); border-left: 4px solid #0d6efd;">
                            <h6 class="text-primary small fw-bold text-uppercase mb-2">
                                <i class="bi bi-link-45deg me-1"></i> Saran Integratif
                            </h6>
                            <p class="mb-0 small">{{ $smartAnalysis['saran_integratif'] }}</p>
                        </div>
                    @endif

                    {{-- Red Flags --}}
                    @if(!empty($smartAnalysis['red_flags']))
                        <div class="p-3 rounded-3 mt-3" style="background: rgba(255,193,7,0.15); border-left: 4px solid #ffc107;">
                            <h6 class="text-dark small fw-bold text-uppercase mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Peringatan Indikator Kritis (Skor
                                1 — BB)
                            </h6>
                            @foreach($smartAnalysis['red_flags'] as $flag)
                                <p class="mb-1 small"><strong>{{ $flag['lingkup'] }}</strong> — {{ $flag['indikator'] }} <span
                                        class="badge bg-danger">BB</span></p>
                            @endforeach
                            <p class="mb-0 mt-2 small fst-italic text-muted">Indikator di atas menunjukkan anak Belum Berkembang dan
                                memerlukan stimulasi mendesak.</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center text-muted">
                    <i class="bi bi-robot fs-1 d-block mb-2"></i>
                    <p class="mb-2 fw-semibold">Belum Ada Analisis AI</p>
                    <small>Jalankan "Generate Analisis AI" di halaman kelas terlebih dahulu.</small>
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
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-people text-info me-2"></i> Perbandingan dengan Kelompok Sebaya
                    </h5>
                    <p class="text-muted small mb-4">
                        Ananda berada dalam kelompok <strong>"{{ $peer['aspek_dominan_kelompok'] }}"</strong>
                        bersama <strong>{{ $peer['jumlah_siswa_kelompok'] }} siswa</strong> dengan profil perkembangan serupa.
                        Cluster ini diidentifikasi otomatis oleh AI berdasarkan pola nilai seluruh kelas.
                    </p>
                    <div class="row g-3">
                        @foreach($peer['detail'] as $lingkup => $cmp)
                            @php
                                $colorMap = ['above' => 'success', 'equal' => 'secondary', 'below' => 'danger'];
                                $color = $colorMap[$cmp['status']] ?? 'secondary';
                            @endphp
                            <div class="col-md-4 col-sm-6">
                                <div
                                    class="p-3 rounded-3 border border-{{ $color }} border-opacity-25 bg-{{ $color }} bg-opacity-10 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <small class="fw-bold text-dark">{{ $lingkup }}</small>
                                        <span class="badge bg-{{ $color }} rounded-pill">{{ $cmp['icon'] }}
                                            {{ $cmp['status'] === 'above' ? 'Di atas' : ($cmp['status'] === 'below' ? 'Di bawah' : 'Setara') }}</span>
                                    </div>
                                    <div class="d-flex align-items-end gap-3">
                                        <div>
                                            <div class="text-muted" style="font-size:0.72rem;">Ananda</div>
                                            <div class="fw-bold text-{{ $color }} fs-5">{{ $cmp['skor_anak'] }}</div>
                                        </div>
                                        <div class="text-muted small">vs</div>
                                        <div>
                                            <div class="text-muted" style="font-size:0.72rem;">Rata-rata Kelompok</div>
                                            <div class="fw-bold fs-5">{{ $cmp['rata_kelompok'] }}</div>
                                        </div>
                                    </div>
                                    <small class="text-{{ $color }} d-block mt-1">{{ $cmp['label'] }}</small>
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
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-graph-up-arrow text-warning me-2"></i> Tren Perkembangan Antar Semester
                    </h5>
                    <p class="text-muted small mb-4">
                        Perbandingan perkembangan Ananda antara semester lalu dan semester ini.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead style="background: linear-gradient(90deg, #fd7e14, #ffc107); color: white;">
                                <tr>
                                    <th class="py-2 px-3">Aspek</th>
                                    <th class="py-2 px-3 text-center">Semester Lalu</th>
                                    <th class="py-2 px-3 text-center">Semester Ini</th>
                                    <th class="py-2 px-3 text-center">Perubahan</th>
                                    <th class="py-2 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($smartAnalysis['trend_data'] as $lingkup => $trend)
                                    @php
                                        $trendColor = match ($trend['trend']) {
                                            'up_significant' => 'success',
                                            'up' => 'info',
                                            'stable' => 'secondary',
                                            'down' => 'warning',
                                            'down_significant' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-3 fw-semibold">{{ $lingkup }}</td>
                                        <td class="px-3 text-center">{{ $trend['skor_lalu'] }}/4.0</td>
                                        <td class="px-3 text-center fw-bold text-{{ $trendColor }}">
                                            {{ $trend['skor_sekarang'] }}/4.0</td>
                                        <td class="px-3 text-center">
                                            <span class="badge bg-{{ $trendColor }}">
                                                {{ $trend['delta'] > 0 ? '+' : '' }}{{ $trend['delta'] }}
                                            </span>
                                        </td>
                                        <td class="px-3">
                                            {{ $trend['icon'] }} <span
                                                class="small text-{{ $trendColor }}">{{ $trend['label'] }}</span>
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