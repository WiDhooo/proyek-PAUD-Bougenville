@extends('layouts.guru')

@section('title', 'Rekap Semester — ' . $kelas->nama_kelas)

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.absensi.index', ['periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}">Absensi</a></li>
            <li class="breadcrumb-item active">Rekap Semester — {{ $kelas->nama_kelas }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="bi bi-bar-chart-line text-primary me-2"></i>Rekap Semester
                    </h4>
                    <p class="text-muted mb-0">
                        <strong>{{ $kelas->nama_kelas }}</strong> ({{ $kelas->kelas ?? '' }})
                        — Hari: <strong>{{ $hariList }}</strong>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2">{{ $judulSemester }}</span>
                    </p>
                    <p class="text-muted mb-0 mt-1" style="font-size:0.85rem;">
                        <i class="bi bi-info-circle me-1"></i>Data digabung dari semua jadwal: {{ $hariList }}
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('guru.absensi.export_semester_pdf', ['kelasId' => $kelas->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-danger rounded-3" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                    </a>
                    <a href="{{ route('guru.absensi.export_semester_excel', ['kelasId' => $kelas->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-success rounded-3">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
                    </a>
                    <a href="{{ route('guru.absensi.index', ['periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-outline-secondary rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap Semester --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" rowspan="2" style="width:40px; vertical-align:middle;">#</th>
                            <th class="py-3 px-3" rowspan="2" style="vertical-align:middle;">Nama Siswa</th>
                            {{-- Header per bulan --}}
                            @foreach($namaBulanList as $nb)
                            <th class="py-2 px-1 text-center" colspan="4" style="background:#f0f9ff; border-bottom:1px solid #dee2e6;">
                                {{ $nb }}
                            </th>
                            @endforeach
                            {{-- Total semester --}}
                            <th class="py-3 px-2 text-center" rowspan="2" style="width:50px; vertical-align:middle;">Total H</th>
                            <th class="py-3 px-2 text-center" rowspan="2" style="width:50px; vertical-align:middle;">Total</th>
                            <th class="py-3 px-2 text-center" rowspan="2" style="width:80px; vertical-align:middle;">% Hadir</th>
                        </tr>
                        <tr>
                            @foreach($namaBulanList as $nb)
                            <th class="py-1 px-1 text-center" style="width:28px; font-size:10px; background:#f8fafc;"><span class="text-success">H</span></th>
                            <th class="py-1 px-1 text-center" style="width:28px; font-size:10px; background:#f8fafc;"><span class="text-warning">S</span></th>
                            <th class="py-1 px-1 text-center" style="width:28px; font-size:10px; background:#f8fafc;"><span class="text-info">I</span></th>
                            <th class="py-1 px-1 text-center" style="width:28px; font-size:10px; background:#f8fafc;"><span class="text-danger">A</span></th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $i => $r)
                        @php
                            $persenClass = $r['persen'] >= 75 ? 'text-success' : ($r['persen'] >= 50 ? 'text-warning' : 'text-danger');
                        @endphp
                        <tr>
                            <td class="px-3 text-muted">{{ $i + 1 }}</td>
                            <td class="px-3 fw-semibold">
                                <i class="bi bi-person-fill text-secondary me-1"></i>{{ $r['siswa']->nama }}
                            </td>
                            @foreach($namaBulanList as $nb)
                            @php $bln = $r['per_bulan'][$nb] ?? ['H'=>0,'S'=>0,'I'=>0,'A'=>0]; @endphp
                            <td class="text-center px-1">
                                <span class="text-success fw-semibold" style="font-size:12px;">{{ $bln['H'] ?: '-' }}</span>
                            </td>
                            <td class="text-center px-1">
                                <span class="text-warning fw-semibold" style="font-size:12px;">{{ $bln['S'] ?: '-' }}</span>
                            </td>
                            <td class="text-center px-1">
                                <span class="text-info fw-semibold" style="font-size:12px;">{{ $bln['I'] ?: '-' }}</span>
                            </td>
                            <td class="text-center px-1">
                                <span class="text-danger fw-semibold" style="font-size:12px;">{{ $bln['A'] ?: '-' }}</span>
                            </td>
                            @endforeach
                            <td class="text-center fw-bold text-success">{{ $r['hadir'] }}</td>
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
                            <td colspan="100" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data absensi untuk semester ini.
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
