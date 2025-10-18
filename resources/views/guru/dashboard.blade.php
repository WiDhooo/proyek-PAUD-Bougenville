@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3>Jadwal Mengajar Anda Pekan Ini</h3>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">Hari</th>
                            <th style="width: 30%;">Waktu</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwal as $hari => $detail)
                        <tr>
                            <td class="fw-bold">{{ $hari }}</td>
                            <td>{{ $detail[0] }}</td>
                            <td>{{ $detail[1] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection