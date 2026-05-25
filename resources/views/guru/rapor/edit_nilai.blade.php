@extends('layouts.guru')

@section('title', 'Edit Nilai — ' . $siswa->nama)

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.rapor.pilih_kelas') }}">Analisis dan Rekomendasi Minat Bakat</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.rapor.daftar_siswa', $siswa->kelas_id) }}">{{ $siswa->kelas->nama_kelas ?? '-' }}</a></li>
            <li class="breadcrumb-item active">Edit Nilai — {{ $siswa->nama }}</li>
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

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                    <i class="bi bi-pencil-square fs-4 text-white"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Edit Nilai: {{ $siswa->nama }}</h4>
                    <p class="mb-0 opacity-75">Periode: {{ $periode }} | Tahun Ajaran: {{ $tahunAjaran }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(empty($nilaiExisting))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Belum ada nilai untuk siswa ini pada periode <strong>{{ $periode }}</strong> tahun <strong>{{ $tahunAjaran }}</strong>.
            <a href="{{ route('guru.rapor.input', ['kelas_id' => $siswa->kelas_id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}" class="alert-link">Input nilai baru →</a>
        </div>
    @else
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="alert alert-info rounded-3 mb-4 border-0" style="background-color: #f8fbff; border-left: 4px solid #0d6efd !important;">
                <h6 class="fw-bold mb-2 text-primary" style="font-size: 0.9rem;"><i class="bi bi-info-circle me-1"></i> Keterangan Skala Penilaian:</h6>
                <div class="row g-2 small text-dark">
                    <div class="col-md-3"><strong>1</strong>: Belum Berkembang (BB)</div>
                    <div class="col-md-3"><strong>2</strong>: Mulai Berkembang (MB)</div>
                    <div class="col-md-3"><strong>3</strong>: Berkembang Sesuai Harapan (BSH)</div>
                    <div class="col-md-3"><strong>4</strong>: Berkembang Sangat Baik (BSB)</div>
                </div>
            </div>

            <form action="{{ route('guru.rapor.update_nilai', $siswa->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

                @foreach($aspekPenilaians as $lingkup => $aspeks)
                    <h6 class="fw-bold text-primary mt-4 mb-3">
                        <i class="bi bi-bookmark-fill me-1"></i> {{ $lingkup }}
                    </h6>
                    <div class="row g-3">
                        @foreach($aspeks as $aspek)
                            @php $currentVal = $nilaiExisting[$aspek->id] ?? null; @endphp
                            <div class="col-md-6">
                                <div class="card bg-light border-0 rounded-3 p-3">
                                    <p class="fw-semibold small mb-2">{{ $aspek->sub_lingkup }} — {{ $aspek->indikator }}</p>
                                    <div class="d-flex gap-3">
                                        @foreach([1 => '1', 2 => '2', 3 => '3', 4 => '4'] as $skor => $label)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="nilai[{{ $aspek->id }}]" value="{{ $skor }}"
                                                    id="aspek{{ $aspek->id }}_{{ $skor }}"
                                                    {{ $currentVal == $skor ? 'checked' : '' }}
                                                    required>
                                                <label class="form-check-label small" for="aspek{{ $aspek->id }}_{{ $skor }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('guru.rapor.detail', ['id' => $siswa->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                       class="btn btn-outline-secondary rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-warning btn-lg rounded-3 text-white">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
