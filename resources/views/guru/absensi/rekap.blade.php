@extends('layouts.guru')

@section('title', 'Rekap Absensi — ' . $namaBulan)

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.absensi.index', ['periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}">Absensi</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}">{{ $jadwal->kelas->nama_kelas }}</a>
            </li>
            <li class="breadcrumb-item active">Rekap {{ $namaBulan }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="bi bi-clipboard-data text-primary me-2"></i>Rekap Absensi
                    </h4>
                    <p class="text-muted mb-0">
                        <strong>{{ $jadwal->kelas->nama_kelas }}</strong> ({{ $jadwal->kelas->kelas ?? '' }})
                        — {{ $jadwal->hari }}
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2">{{ $periode }} {{ $tahunAjaran }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('guru.absensi.rekap_semester', ['kelasId' => $jadwal->kelas_id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-outline-primary rounded-3">
                        <i class="bi bi-bar-chart-line me-1"></i> Rekap Semester
                    </a>
                    <a href="{{ route('guru.absensi.export_pdf', ['id' => $jadwal->id, 'bulan' => $bulan, 'tahun' => $tahun, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-danger rounded-3" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </a>
                    <a href="{{ route('guru.absensi.export_excel', ['id' => $jadwal->id, 'bulan' => $bulan, 'tahun' => $tahun, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-success rounded-3">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
                    </a>
                    <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'bulan' => $bulan, 'tahun' => $tahun, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-outline-success rounded-3">
                        <i class="bi bi-calendar2-week me-1"></i> Kalender
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigasi Bulan --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        @if($prevAllowed)
        <a href="{{ route('guru.absensi.rekap', ['id' => $jadwal->id, 'bulan' => $prevMonth, 'tahun' => $prevYear, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
           class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-chevron-left"></i> Sebelumnya
        </a>
        @else
        <button class="btn btn-outline-secondary rounded-3" disabled>
            <i class="bi bi-chevron-left"></i> Sebelumnya
        </button>
        @endif

        <h5 class="fw-bold mb-0">{{ $namaBulan }}</h5>

        @if($nextAllowed)
        <a href="{{ route('guru.absensi.rekap', ['id' => $jadwal->id, 'bulan' => $nextMonth, 'tahun' => $nextYear, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
           class="btn btn-outline-secondary rounded-3">
            Berikutnya <i class="bi bi-chevron-right"></i>
        </a>
        @else
        <button class="btn btn-outline-secondary rounded-3" disabled>
            Berikutnya <i class="bi bi-chevron-right"></i>
        </button>
        @endif
    </div>

    {{-- Tabel Rekap --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" style="width:40px;">#</th>
                            <th class="py-3 px-3">Nama Siswa</th>
                            <th class="py-3 px-3 text-center" style="width:60px;">
                                <span class="badge bg-success text-white">H</span>
                            </th>
                            <th class="py-3 px-3 text-center" style="width:60px;">
                                <span class="badge bg-warning text-dark">S</span>
                            </th>
                            <th class="py-3 px-3 text-center" style="width:60px;">
                                <span class="badge bg-info text-white">I</span>
                            </th>
                            <th class="py-3 px-3 text-center" style="width:60px;">
                                <span class="badge bg-danger text-white">A</span>
                            </th>
                            <th class="py-3 px-3 text-center" style="width:60px;">Total</th>
                            <th class="py-3 px-3 text-center" style="width:100px;">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $i => $r)
                        @php
                            $persenClass = 'text-success';
                            if ($r['persen'] < 50) $persenClass = 'text-danger';
                            elseif ($r['persen'] < 75) $persenClass = 'text-warning';
                        @endphp
                        <tr>
                            <td class="px-3 text-muted">{{ $i + 1 }}</td>
                            <td class="px-3 fw-semibold">
                                <i class="bi bi-person-fill text-secondary me-1"></i>
                                {{ $r['siswa']->nama }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">{{ $r['hadir'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">{{ $r['sakit'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2">{{ $r['izin'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">{{ $r['alpha'] }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $r['total'] }}</td>
                            <td class="text-center">
                                <span class="fw-bold {{ $persenClass }}">{{ $r['persen'] }}%</span>
                                <div class="progress mt-1" style="height:4px;">
                                    <div class="progress-bar {{ $r['persen'] >= 75 ? 'bg-success' : ($r['persen'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width:{{ $r['persen'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data absensi untuk bulan ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
