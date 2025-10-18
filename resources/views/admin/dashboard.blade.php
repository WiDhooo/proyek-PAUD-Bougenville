@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3>Selamat Datang, Zelaya Wijayanti! 👋</h3>
        <p class="text-muted">Berikut adalah ringkasan aktivitas sekolah hari ini.</p>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-3 me-4">
                        <i class="bi bi-people-fill fs-2 text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Murid</p>
                        <h4 class="fw-bold mb-0">{{ $data['total_murid'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 me-4">
                        <i class="bi bi-person-badge-fill fs-2 text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Guru</p>
                        <h4 class="fw-bold mb-0">{{ $data['total_guru'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-warning bg-opacity-10 rounded-3 me-4">
                        <i class="bi bi-house-door-fill fs-2 text-warning"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Kelas</p>
                        <h4 class="fw-bold mb-0">{{ $data['total_kelas'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Jadwal Mengajar</h5>
                    <div>
                        <a href="#" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg"></i> Tambah Jadwal
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash-fill"></i> Delete Jadwal
                        </a>
                    </div>
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
                            @foreach ($data['jadwal'] as $hari => $kelas)
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
</div>
@endsection