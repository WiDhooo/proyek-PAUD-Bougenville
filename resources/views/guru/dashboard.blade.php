@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">

    <!-- Judul dan deskripsi -->
    <div class="mb-4">
        <h3>Selamat Datang, {{ Auth::user()->nama ?? 'Guru' }}! 👋</h3>
        <p class="text-muted">Berikut adalah jadwal mengajar keseluruhan pekan ini.</p>
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
                        <h4 class="fw-bold mb-0">{{ $jadwal->count() }}</h4>
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
                        <h6 class="text-muted mb-1">Total Pertemuan</h6>
                        <h4 class="fw-bold mb-0">{{ $jadwal->count() }} Kali</h4>
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
                <span class="text-muted small">
                    Update terakhir: {{ now()->translatedFormat('d F Y') }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background: linear-gradient(90deg, #0d6efd, #5ab2ff); color: white;">
                        <tr>
                            <th class="py-3 px-3">Hari</th>
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3">Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($jadwal as $item)
                        <tr class="table-row">
                            <td class="fw-semibold px-3 py-3 text-dark">{{ $item->hari }}</td>
                            <td class="px-3 py-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill">
                                    {{ $item->kelas->nama_kelas ?? '-' }} - {{ $item->kelas->kelas ?? '' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-secondary">
                                @if($item->waktu_mulai && $item->waktu_selesai)
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $item->waktu_mulai->format('H:i') }} - {{ $item->waktu_selesai->format('H:i') }}
                                @else
                                    <span class="text-muted">Belum diatur</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="bi bi-exclamation-circle me-2"></i> Belum ada jadwal tersedia.
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
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

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

    .card-body h5 {
        font-weight: 600;
    }
</style>

@endsection