@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">
    <!-- Judul dan deskripsi -->
    <div class="mb-4">
<<<<<<< HEAD
        <h3>Selamat Datang, Nama Guru! 👋</h3>
        <p class="text-muted">Berikut adalah jadwal mengajar keseluruhan pekan ini.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Jadwal Mengajar</h5>
                {{-- Tombol CRUD sengaja dihilangkan untuk guru --}}
            </div>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">Hari</th>
                            <th>Mandiri - A</th>
                            <th>Ceria - B</th>
                            <th>Kreatif - A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwal as $hari => $kelas)
                        <tr>
                            <td class="fw-bold">{{ $hari }}</td>
                            <td>{{ $kelas['Mandiri - A'] ?? '-' }}</td>
                            <td>{{ $kelas['Ceria - B'] ?? '-' }}</td>
                            <td>{{ $kelas['Kreatif - A'] ?? '-' }}</td>
=======
        {{-- <h3 class="fw-bold text-primary mb-1">Selamat datang kembali, pantau jadwal dan kegiatan mengajar Anda di sini.</h3> --}}
        <p class="text-muted mb-0">Selamat datang kembali, pantau jadwal dan kegiatan mengajar Anda di sini.</p>
    </div>

    <!-- Statistik ringkasan -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-gradient-light">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Jadwal</h6>
                        <h4 class="fw-bold mb-0">{{ count($jadwal) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-gradient-light">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Jumlah Kelas</h6>
                        <h4 class="fw-bold mb-0">
                            {{ $jadwal->pluck('kelas.nama_kelas')->unique()->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-gradient-light">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Jam Mengajar</h6>
                        <h4 class="fw-bold mb-0">{{ $jadwal->count() * 1.5 }} Jam</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel jadwal -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-primary">Jadwal Mengajar Pekan Ini</h5>
                <span class="text-muted small">Update terakhir: {{ now()->translatedFormat('d F Y') }}</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background: linear-gradient(90deg, #0d6efd, #5ab2ff); color: white;">
                        <tr>
                            <th class="py-3 px-3">Hari</th>
                            <th class="py-3 px-3">Waktu</th>
                            <th class="py-3 px-3">Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwal as $item)
                        <tr class="table-row">
                            <td class="fw-semibold px-3 py-3 text-dark">{{ $item->hari }}</td>
                            <td class="px-3 py-3 text-secondary">{{ $item->waktu }}</td>
                            <td class="px-3 py-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill">
                                    {{ $item->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
>>>>>>> ce5e812 (Update untuk GURU di bagian dashboard, data siswa, tambah model & migration)
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="bi bi-exclamation-circle"></i> Belum ada jadwal tersedia.
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
    /* Gaya kartu ringkasan */
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Gaya tabel */
    .table thead th {
        font-size: 15px;
        font-weight: 600;
        border: none;
    }

    .table-row {
        transition: all 0.2s ease-in-out;
    }

    .table-row:hover {
        background-color: #f5f9ff;
        transform: scale(1.005);
    }

    /* Responsif spacing */
    .card-body h5 {
        font-weight: 600;
    }
</style>
@endsection
