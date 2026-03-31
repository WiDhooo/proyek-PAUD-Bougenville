@extends('layouts.guru')

@section('title', 'Kalender Absensi — ' . $jadwal->kelas->nama_kelas)

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.88rem;">
            <li class="breadcrumb-item"><a href="{{ route('guru.absensi.index', ['periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}" style="color: var(--paud-teal); text-decoration:none;">Absensi</a></li>
            <li class="breadcrumb-item active" style="color: var(--paud-muted);">{{ $jadwal->kelas->nama_kelas }} — {{ $jadwal->hari }}</li>
        </ol>
    </nav>

    {{-- Header Card --}}
    <div class="paud-card mb-4">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--paud-text);">
                        <i class="bi bi-calendar2-week me-2" style="color: var(--paud-teal);"></i>Kalender Absensi
                    </h4>
                    <p style="color: var(--paud-muted); font-size:0.9rem;" class="mb-0">
                        {{ $jadwal->kelas->nama_kelas }} ({{ $jadwal->kelas->kelas ?? '' }})
                        — Setiap <strong>{{ $jadwal->hari }}</strong>
                        @if($jadwal->waktu_mulai && $jadwal->waktu_selesai)
                            , {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                        @endif
                        — {{ $totalSiswa }} siswa
                        <span class="paud-badge bg-paud-teal-light text-paud-teal ms-2">{{ $periode }} {{ $tahunAjaran }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('guru.absensi.rekap', ['id' => $jadwal->id, 'bulan' => $bulan, 'tahun' => $tahun, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn paud-btn-outline btn-sm">
                        <i class="bi bi-clipboard-data me-1"></i> Rekap
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius: var(--paud-radius-sm); border: none;" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" style="border-radius: var(--paud-radius-sm); border: none;" role="alert">
        <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Month Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        @if($prevAllowed)
        <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'bulan' => $prevMonth, 'tahun' => $prevYear, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
           class="btn paud-btn-outline btn-sm" style="border-radius:20px;">
            <i class="bi bi-chevron-left"></i> Sebelumnya
        </a>
        @else
        <button class="btn paud-btn-outline btn-sm" style="border-radius:20px; opacity:0.4;" disabled>
            <i class="bi bi-chevron-left"></i> Sebelumnya
        </button>
        @endif

        <h5 class="fw-bold mb-0" style="color: var(--paud-text);">{{ $namaBulan }}</h5>

        @if($nextAllowed)
        <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'bulan' => $nextMonth, 'tahun' => $nextYear, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
           class="btn paud-btn-outline btn-sm" style="border-radius:20px;">
            Berikutnya <i class="bi bi-chevron-right"></i>
        </a>
        @else
        <button class="btn paud-btn-outline btn-sm" style="border-radius:20px; opacity:0.4;" disabled>
            Berikutnya <i class="bi bi-chevron-right"></i>
        </button>
        @endif
    </div>

    {{-- Legend --}}
    <div class="d-flex gap-4 mb-3 flex-wrap" style="font-size:0.82rem;">
        <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background: var(--paud-teal);"></span> Hari Jadwal ({{ $jadwal->hari }})</span>
        <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background: #D5D8DC;"></span> Bukan Hari Jadwal</span>
        <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background: var(--paud-green);"></span> Sudah Diabsen</span>
    </div>

    {{-- Calendar Grid --}}
    <div class="paud-card">
        <div class="p-3">
            <div class="row text-center fw-semibold mb-2" style="color: var(--paud-muted); font-size:0.82rem;">
                <div class="col">Sen</div>
                <div class="col">Sel</div>
                <div class="col">Rab</div>
                <div class="col">Kam</div>
                <div class="col">Jum</div>
                <div class="col" style="color: var(--paud-coral);">Sab</div>
                <div class="col" style="color: var(--paud-coral);">Min</div>
            </div>

            @foreach(array_chunk($calendarDays, 7) as $week)
            <div class="row text-center mb-1">
                @foreach($week as $day)
                @php
                    $bgClass = '';
                    $bgStyle = 'background: #F9F8F6;';
                    $textStyle = 'color: var(--paud-muted);';
                    $borderStyle = '';
                    $ringStyle = '';

                    if (!$day['is_current_month']) {
                        $bgStyle = 'background: #F9F8F6; opacity: 0.3;';
                    } elseif ($day['is_today'] && $day['can_absen']) {
                        $bgStyle = 'background: var(--paud-teal-light);';
                        $ringStyle = 'box-shadow: inset 0 0 0 2px var(--paud-teal);';
                        $textStyle = 'color: var(--paud-teal); font-weight:700;';
                    } elseif ($day['is_jadwal_day']) {
                        $bgStyle = 'background: #EDF7F0;';
                        $borderStyle = 'border-left: 3px solid var(--paud-green);';
                        $textStyle = 'color: var(--paud-text);';
                    }
                @endphp
                <div class="col p-1">
                    @if($day['can_absen'] && $day['is_current_month'])
                    <a href="{{ route('guru.absensi.input', ['id' => $jadwal->id, 'tanggal' => $day['tanggal'], 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="d-block text-decoration-none cal-cell"
                       style="{{ $bgStyle }} {{ $textStyle }} {{ $borderStyle }} {{ $ringStyle }} border-radius: var(--paud-radius-sm);">
                        <span class="fw-semibold">{{ $day['day'] }}</span>
                        @if($day['sudah_absen'])
                            <br><span class="cal-badge-done">
                                <i class="bi bi-check-circle-fill"></i> {{ $day['jumlah_absen'] }}
                            </span>
                        @endif
                    </a>
                    @else
                    <div class="d-block cal-cell" style="{{ $bgStyle }} {{ $textStyle }} border-radius: var(--paud-radius-sm); {{ $day['is_current_month'] ? 'opacity:0.4;' : 'opacity:0.15;' }}">
                        <span>{{ $day['day'] }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.cal-cell {
    min-height: 68px;
    padding: 8px 4px;
    transition: all .15s ease;
}
.cal-cell:hover {
    transform: scale(1.04);
    box-shadow: var(--paud-shadow-hover);
}
.cal-badge-done {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--paud-green);
    margin-top: 2px;
}
.legend-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 3px;
}
</style>
@endsection
