@extends('layouts.guru')

@section('title', 'Kalender Absensi — ' . $jadwal->kelas->nama_kelas)

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.absensi.index', ['periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}">Absensi</a></li>
            <li class="breadcrumb-item active">{{ $jadwal->kelas->nama_kelas }} — {{ $jadwal->hari }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="bi bi-calendar2-week text-success me-2"></i>Kalender Absensi
                    </h4>
                    <p class="text-muted mb-0">
                        {{ $jadwal->kelas->nama_kelas }} ({{ $jadwal->kelas->kelas ?? '' }})
                        — Setiap <strong>{{ $jadwal->hari }}</strong>
                        @if($jadwal->waktu_mulai && $jadwal->waktu_selesai)
                            , {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                        @endif
                        — {{ $totalSiswa }} siswa
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2">{{ $periode }} {{ $tahunAjaran }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('guru.absensi.rekap', ['id' => $jadwal->id, 'bulan' => $bulan, 'tahun' => $tahun, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-outline-primary rounded-3">
                        <i class="bi bi-clipboard-data me-1"></i> Rekap
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Navigasi Bulan --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        @if($prevAllowed)
        <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'bulan' => $prevMonth, 'tahun' => $prevYear, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
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
        <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'bulan' => $nextMonth, 'tahun' => $nextYear, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
           class="btn btn-outline-secondary rounded-3">
            Berikutnya <i class="bi bi-chevron-right"></i>
        </a>
        @else
        <button class="btn btn-outline-secondary rounded-3" disabled>
            Berikutnya <i class="bi bi-chevron-right"></i>
        </button>
        @endif
    </div>

    {{-- Legenda --}}
    <div class="d-flex gap-3 mb-3 flex-wrap">
        <small><span class="legend-dot bg-success"></span> Hari Jadwal ({{ $jadwal->hari }})</small>
        <small><span class="legend-dot bg-secondary opacity-25"></span> Bukan Hari Jadwal</small>
        <small><span class="badge bg-success text-white" style="font-size:9px;">✅</span> Sudah Diabsen</small>
    </div>

    {{-- Calendar Grid --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="row text-center fw-bold text-muted mb-2">
                <div class="col">Sen</div>
                <div class="col">Sel</div>
                <div class="col">Rab</div>
                <div class="col">Kam</div>
                <div class="col">Jum</div>
                <div class="col">Sab</div>
                <div class="col">Min</div>
            </div>

            @foreach(array_chunk($calendarDays, 7) as $week)
            <div class="row text-center mb-1">
                @foreach($week as $day)
                @php
                    $bgClass = 'bg-light';
                    $textClass = 'text-muted';
                    $borderClass = '';

                    if (!$day['is_current_month']) {
                        $bgClass = 'bg-light opacity-50';
                    } elseif ($day['is_today'] && $day['can_absen']) {
                        $bgClass = 'bg-info bg-opacity-25';
                        $borderClass = 'border border-info border-2';
                        $textClass = 'text-dark';
                    } elseif ($day['is_jadwal_day']) {
                        $bgClass = 'bg-success bg-opacity-15';
                        $borderClass = 'border border-success';
                        $textClass = 'text-dark';
                    }
                @endphp
                <div class="col p-1">
                    @if($day['can_absen'] && $day['is_current_month'])
                    <a href="{{ route('guru.absensi.input', ['id' => $jadwal->id, 'tanggal' => $day['tanggal'], 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="d-block rounded-3 py-2 text-decoration-none {{ $bgClass }} {{ $textClass }} {{ $borderClass }} cal-cell">
                        <span class="fw-semibold">{{ $day['day'] }}</span>
                        @if($day['sudah_absen'])
                            <br><span class="badge bg-success text-white mt-1" style="font-size:9px;">✅ {{ $day['jumlah_absen'] }}</span>
                        @endif
                    </a>
                    @else
                    <div class="d-block rounded-3 py-2 {{ $bgClass }} {{ $textClass }} cal-cell" style="opacity: {{ $day['is_current_month'] ? '0.4' : '0.2' }};">
                        <span>{{ $day['day'] }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
<style>
.cal-cell { min-height: 65px; transition: all .15s ease; }
.cal-cell:hover { transform: scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,.08); }
.legend-dot { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 4px; vertical-align: middle; }
</style>
@endsection
