@extends('layouts.guru')

@section('title', 'Input Absensi — ' . $tanggalFormatted)

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.absensi.index', ['periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}">Absensi</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}">{{ $jadwal->kelas->nama_kelas }}</a>
            </li>
            <li class="breadcrumb-item active">{{ $tanggalFormatted }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-1">
                <i class="bi bi-pencil-square text-success me-2"></i>Input Absensi
            </h4>
            <p class="text-muted mb-0">
                <strong>{{ $jadwal->kelas->nama_kelas }}</strong> — {{ $tanggalFormatted }}
                @if($jadwal->waktu_mulai && $jadwal->waktu_selesai)
                    ({{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }})
                @endif
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2">{{ $periode }} {{ $tahunAjaran }}</span>
            </p>
            @if($existing->isNotEmpty())
            <div class="mt-2">
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                    <i class="bi bi-exclamation-circle me-1"></i> Mode Edit — data sebelumnya sudah dimuat
                </span>
            </div>
            @endif
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('guru.absensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="periode" value="{{ $periode }}">
        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 px-3" style="width:40px;">#</th>
                                <th class="py-3 px-3">Nama Siswa</th>
                                <th class="py-3 px-3 text-center" style="width:70px;">
                                    <span class="badge bg-success text-white">H</span>
                                </th>
                                <th class="py-3 px-3 text-center" style="width:70px;">
                                    <span class="badge bg-warning text-dark">S</span>
                                </th>
                                <th class="py-3 px-3 text-center" style="width:70px;">
                                    <span class="badge bg-info text-white">I</span>
                                </th>
                                <th class="py-3 px-3 text-center" style="width:70px;">
                                    <span class="badge bg-danger text-white">A</span>
                                </th>
                                <th class="py-3 px-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $i => $siswa)
                            @php
                                $currentStatus = $existing[$siswa->id]->status ?? 'H';
                                $currentKet    = $existing[$siswa->id]->keterangan ?? '';
                            @endphp
                            <tr>
                                <td class="px-3 text-muted">{{ $i + 1 }}</td>
                                <td class="px-3 fw-semibold">
                                    <i class="bi bi-person-fill text-secondary me-1"></i>
                                    {{ $siswa->nama }}
                                </td>
                                <td class="text-center">
                                    <input type="radio" class="form-check-input border-success"
                                           name="status[{{ $siswa->id }}]" value="H"
                                           {{ $currentStatus === 'H' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio" class="form-check-input border-warning"
                                           name="status[{{ $siswa->id }}]" value="S"
                                           {{ $currentStatus === 'S' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio" class="form-check-input border-info"
                                           name="status[{{ $siswa->id }}]" value="I"
                                           {{ $currentStatus === 'I' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio" class="form-check-input border-danger"
                                           name="status[{{ $siswa->id }}]" value="A"
                                           {{ $currentStatus === 'A' ? 'checked' : '' }}>
                                </td>
                                <td class="px-3">
                                    <input type="text" class="form-control form-control-sm rounded-3"
                                           name="keterangan[{{ $siswa->id }}]"
                                           value="{{ $currentKet }}"
                                           placeholder="Opsional...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('guru.absensi.kalender', ['id' => $jadwal->id, 'bulan' => \Carbon\Carbon::parse($tanggal)->month, 'tahun' => \Carbon\Carbon::parse($tanggal)->year, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
               class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-success rounded-3 px-4">
                <i class="bi bi-check-circle me-1"></i> Simpan Absensi
            </button>
        </div>
    </form>

</div>
@endsection
