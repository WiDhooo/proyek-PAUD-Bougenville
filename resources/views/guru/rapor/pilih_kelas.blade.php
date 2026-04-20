@extends('layouts.guru')

@section('title', 'Analisis dan Rekomendasi Minat Bakat — Pilih Kelas')

@section('content')
<div class="container-fluid" x-data="{ searchQuery: '' }">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--paud-text);">
                <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">Analisis dan Rekomendasi Minat Bakat</span>
            </h3>
            <p style="color: var(--paud-muted); margin-left: 15px; font-size:0.92rem;" class="mb-0">
                Pilih kelas untuk melihat rapor siswa dan menjalankan analisis AI.
            </p>
        </div>
        {{-- Search Bar --}}
        <div class="input-group" style="width: 240px;">
            <span class="input-group-text border-0" style="background: var(--paud-card); border-radius: var(--paud-radius-sm) 0 0 var(--paud-radius-sm);">
                <i class="bi bi-search" style="color: var(--paud-muted);"></i>
            </span>
            <input type="search" class="form-control border-0 shadow-none" placeholder="Cari kelas..."
                x-model.debounce.200ms="searchQuery"
                style="background: var(--paud-card); border-radius: 0 var(--paud-radius-sm) var(--paud-radius-sm) 0; font-size:0.88rem;">
        </div>
    </div>

    <div class="row g-4">
        @forelse ($kelasList as $kelas)
        <div class="col-md-4"
             x-show="searchQuery === '' || '{{ strtolower($kelas->nama_kelas) }} {{ strtolower($kelas->kelas) }}'.includes(searchQuery.toLowerCase())"
             x-transition>
            <div class="paud-card h-100 rapor-kelas-card">
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-paud-teal-light text-paud-teal me-3">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" style="color: var(--paud-text);">{{ $kelas->nama_kelas }}</h5>
                            <small style="color: var(--paud-muted);">{{ $kelas->kelas }}</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span style="color: var(--paud-muted); font-size:0.88rem;">
                            <i class="bi bi-people me-1"></i> {{ $kelas->siswa_count }} Siswa
                        </span>
                        <span class="paud-badge bg-paud-green-light" style="color: var(--paud-green);">
                            <i class="bi bi-check-circle-fill me-1" style="font-size:0.7rem;"></i> Aktif
                        </span>
                    </div>

                    <a href="{{ route('guru.rapor.daftar_siswa', $kelas->id) }}"
                       class="btn paud-btn-primary w-100">
                        <i class="bi bi-arrow-right-circle me-1"></i> Lihat Siswa
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="paud-card text-center py-5">
                <i class="bi bi-calendar-x" style="font-size:3rem; color: var(--paud-border);"></i>
                <p class="mt-3 mb-1 fw-semibold" style="color: var(--paud-text);">
                    Belum ada kelas yang ditugaskan
                </p>
                <p class="mb-0" style="color: var(--paud-muted); font-size:0.88rem;">
                    Hubungi admin untuk menambahkan jadwal mengajar Anda.
                </p>
            </div>
        </div>
        @endforelse
    </div>

</div>

<style>
    .rapor-kelas-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .rapor-kelas-card:hover {
        transform: translateY(-3px);
    }
</style>
@endsection
