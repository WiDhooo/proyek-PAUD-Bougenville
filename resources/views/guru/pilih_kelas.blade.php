@extends('layouts.guru')

@section('title', 'Pilih Kelas')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <p class="text-muted">Klik salah satu kelas untuk masuk ke form absensi dan nilai.</p>
    </div>

    <div class="row g-3">
        @forelse ($kelas as $k)
            <div class="col-md-6 col-sm-12">
                <a href="{{ route('guru.nilai_absensi.kelas', $k->id) }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100 hover-scale">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-building fs-2 text-primary"></i>
                            <h5 class="card-title mb-0">{{ $k->nama_kelas }}</h5>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center text-muted">
                Belum ada kelas tersedia.
            </div>
        @endforelse
    </div>
</div>

<style>
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
