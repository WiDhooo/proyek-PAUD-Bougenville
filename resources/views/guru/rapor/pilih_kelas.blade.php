@extends('layouts.guru')

@section('title', 'Rapor Digital — Pilih Kelas')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3>Rapor Digital Cerdas</h3>
        <p class="text-muted">Pilih kelas untuk melihat rapor siswa dan menjalankan analisis AI.</p>
    </div>

    <div class="row g-4">
        @forelse ($kelasList as $kelas)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $kelas->nama_kelas }}</h5>
                            <small class="text-muted">{{ $kelas->kelas }}</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">
                            <i class="bi bi-people me-1"></i> {{ $kelas->siswa_count }} Siswa
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3">
                            Aktif
                        </span>
                    </div>

                    <a href="{{ route('guru.rapor.daftar_siswa', $kelas->id) }}" 
                       class="btn btn-primary w-100 rounded-3">
                        <i class="bi bi-arrow-right-circle me-1"></i> Lihat Siswa
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-circle me-2"></i> Belum ada kelas. Hubungi admin.
            </div>
        </div>
        @endforelse
    </div>

</div>

<style>
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
