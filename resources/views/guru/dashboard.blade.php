@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection