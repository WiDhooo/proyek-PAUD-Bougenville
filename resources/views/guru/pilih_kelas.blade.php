@extends('layouts.guru')

@section('title', 'Pilih Kelas')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4>Pilih Kelas untuk Input Nilai & Absensi</h4>
        <p class="text-muted">Klik salah satu kelas untuk masuk ke form absensi dan nilai.</p>
    </div>

    <div class="row g-3">
        @forelse ($kelas as $k)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('guru.nilai_absensi.kelas', $k->id) }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-scale">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-building fs-2"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">{{ $k->nama_kelas }}</h5>
                                <p class="text-muted mb-0 small">Kelas {{ $k->kelas }}</p>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-people-fill me-1"></i>{{ $k->siswa->count() }} Siswa
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada kelas yang diampu. Silakan hubungi admin untuk pengaturan jadwal.
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.icon-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hover-scale {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.hover-scale:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
}
</style>
@endsection