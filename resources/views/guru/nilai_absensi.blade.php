@extends('layouts.guru')

@section('title', 'Input Nilai & Absensi')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Input Nilai & Absensi - Kelas Mandiri A</h5>
            {{-- Nanti di sini bisa ditambahkan filter tanggal atau bulan --}}
        </div>
        <div class="card-body">
            <form action="#" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama Siswa</th>
                                <th colspan="4">Absensi</th>
                                <th rowspan="2">Nilai Harian</th>
                                <th rowspan="2">Catatan Guru</th>
                            </tr>
                            <tr>
                                <th>H (Hadir)</th>
                                <th>S (Sakit)</th>
                                <th>I (Izin)</th>
                                <th>A (Alpa)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($murid as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item['nama'] }}</td>
                                    {{-- Kolom Absensi menggunakan Radio Button --}}
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="h" checked></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="s"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="i"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="a"></td>
                                    {{-- Kolom Nilai --}}
                                    <td style="min-width: 100px;">
                                        <input type="number" class="form-control form-control-sm" name="nilai[{{ $item['id'] }}]" min="0" max="100">
                                    </td>
                                    {{-- Kolom Catatan --}}
                                    <td style="min-width: 250px;">
                                        <input type="text" class="form-control form-control-sm" name="catatan[{{ $item['id'] }}]" placeholder="Catatan singkat...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection