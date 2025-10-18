@extends('layouts.guru')

@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Siswa di Kelas Anda</h5>
            <div class="input-group" style="width: 300px;">
                 <input type="search" class="form-control" placeholder="Cari Nama Siswa...">
                 <button class="btn btn-primary" type="button">Cari</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($murid as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item['nis'] }}</td>
                                <td>{{ $item['nama'] }}</td>
                                <td>{{ $item['jenis_kelamin'] }}</td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-info-circle-fill"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada siswa di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection