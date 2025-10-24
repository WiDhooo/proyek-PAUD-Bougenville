@extends('layouts.guru')

@section('title', 'Input Nilai & Absensi')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div>
                <h5 class="card-title mb-0">Input Nilai & Absensi - {{ $kelas->nama_kelas }}</h5>

                @if(session('success'))
                <div class="alert alert-success mt-2">{{ session('success') }}</div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.nilai_absensi.simpan', $kelas->id) }}" method="POST">
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
                                    {{-- Kolom Absensi --}}
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="h" checked></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="s"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="i"></td>
                                    <td class="text-center"><input class="form-check-input" type="radio" name="absensi[{{ $item['id'] }}]" value="a"></td>
                                    {{-- Nilai --}}
                                    <td style="min-width: 100px;">
                                        <input type="number" class="form-control form-control-sm" name="nilai[{{ $item['id'] }}]" min="0" max="100">
                                    </td>
                                    {{-- Catatan --}}
                                    <td style="min-width: 250px;">
                                        <input type="text" class="form-control form-control-sm" name="catatan[{{ $item['id'] }}]" placeholder="Catatan singkat...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('guru.nilai_absensi.pilih_kelas') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            alert('Data tersimpan!');
        });
    });
});
</script>

@endsection
