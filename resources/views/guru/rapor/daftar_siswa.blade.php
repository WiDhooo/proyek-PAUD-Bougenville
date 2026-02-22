@extends('layouts.guru')

@section('title', 'Rapor Digital — ' . $kelas->nama_kelas)

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.rapor.pilih_kelas') }}">Rapor Digital</a></li>
            <li class="breadcrumb-item active">{{ $kelas->nama_kelas }}</li>
        </ol>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Periode & Tahun Ajaran --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('guru.rapor.daftar_siswa', $kelas->id) }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Periode</label>
                    <select name="periode" class="form-select form-select-sm">
                        <option value="Ganjil" {{ $periode == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $periode == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="form-select form-select-sm">
                        @for($y = 2026; $y <= (int)date('Y') + 5; $y++)
                            @php $ta = $y . '/' . ($y + 1); @endphp
                            <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $kelas->nama_kelas }}</h4>
            <p class="text-muted mb-0">{{ $siswas->count() }} siswa | Periode: <strong>{{ $periode }}</strong> | TA: <strong>{{ $tahunAjaran }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.rapor.input', ['kelas_id' => $kelas->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
               class="btn btn-outline-primary rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Input Nilai
            </a>
            <form action="{{ route('guru.rapor.analisis') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                <button type="submit" class="btn btn-primary rounded-3">
                    🤖 Generate Analisis AI
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel Siswa --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background: linear-gradient(90deg, #0d6efd, #5ab2ff); color: white;">
                        <tr>
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Nama Siswa</th>
                            <th class="py-3 px-3">NIS</th>
                            <th class="py-3 px-3">L/P</th>
                            <th class="py-3 px-3 text-center">Status Analisis</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $i => $siswa)
                        @php
                            $analisis = $siswa->hasilAnalises->first();
                        @endphp
                        <tr class="table-row">
                            <td class="px-3 py-3">{{ $i + 1 }}</td>
                            <td class="px-3 py-3 fw-semibold">{{ $siswa->nama }}</td>
                            <td class="px-3 py-3 text-muted">{{ $siswa->nis }}</td>
                            <td class="px-3 py-3">
                                <span class="badge {{ $siswa->jenis_kelamin == 'Laki-Laki' ? 'bg-info' : 'bg-pink' }} bg-opacity-10 {{ $siswa->jenis_kelamin == 'Laki-Laki' ? 'text-info' : 'text-danger' }} border rounded-pill px-2">
                                    {{ $siswa->jenis_kelamin == 'Laki-Laki' ? 'L' : 'P' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($analisis)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">
                                        <i class="bi bi-check-circle me-1"></i> Cluster {{ $analisis->cluster_group }}
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3">
                                        <i class="bi bi-hourglass-split me-1"></i> Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('guru.rapor.detail', ['id' => $siswa->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                                    class="btn btn-sm btn-outline-primary rounded-3" title="Lihat Rapor">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('guru.rapor.edit_nilai', ['id' => $siswa->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                                    class="btn btn-sm btn-outline-warning rounded-3" title="Edit Nilai">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('guru.rapor.destroy_nilai', $siswa->id) }}" method="POST" class="m-0"
                                        onsubmit="return confirm('Hapus SEMUA nilai {{ $siswa->nama }} untuk periode {{ $periode }} {{ $tahunAjaran }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="periode" value="{{ $periode }}">
                                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus Nilai">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-exclamation-circle me-2"></i> Belum ada siswa di kelas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<style>
    .table thead th { font-size: 14px; font-weight: 600; border: none; }
    .table-row { transition: all 0.2s ease-in-out; }
    .table-row:hover { background-color: #f5f9ff; }
</style>
@endsection
