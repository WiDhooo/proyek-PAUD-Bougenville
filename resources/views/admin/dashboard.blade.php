@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid" x-data="{ deleteUrl: '', deleteInfo: '', editData: {}, editUrl: '' }">
    <div class="mb-4">
        <h3>Selamat Datang, {{ Auth::user()->name ?? 'Administrator' }}! 👋</h3>
        <p class="text-muted">Berikut adalah ringkasan aktivitas sekolah hari ini.</p>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-3 me-4">
                        <i class="bi bi-people-fill fs-2 text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Siswa</p>
                        <h4 class="fw-bold mb-0">{{ $data['total_siswa'] }}</h4>
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

    {{-- Jadwal Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Jadwal Mengajar</h5>
                        <div>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="bi bi-plus-lg"></i> Tambah Jadwal
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapusSemua">
                                <i class="bi bi-trash-fill"></i> Hapus Semua
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%;">Hari</th>
                                    @foreach($kelasList as $kelas)
                                        <th>{{ $kelas->nama_kelas }} - {{ $kelas->kelas }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['jadwal'] as $hari => $kelasData)
                                <tr>
                                    <td class="fw-bold">{{ $hari }}</td>
                                    @foreach($kelasData as $namaKelas => $namaGuru)
                                        <td>
                                            @php
                                                $kelasInfo = explode(' - ', $namaKelas);
                                                $namaKelasFilter = trim($kelasInfo[0]);
                                                $kelasFilter = trim($kelasInfo[1]);
                                                
                                                $jadwalItem = $jadwals->filter(function($jadwal) use ($hari, $namaKelasFilter, $kelasFilter) {
                                                    return $jadwal->hari === $hari && 
                                                           $jadwal->kelas && 
                                                           $jadwal->kelas->nama_kelas === $namaKelasFilter && 
                                                           $jadwal->kelas->kelas === $kelasFilter;
                                                })->first();
                                            @endphp
                                            
                                            <div>{{ $namaGuru }}</div>
                                            @if($jadwalItem && $jadwalItem->waktu_mulai && $jadwalItem->waktu_selesai)
                                                <small class="text-muted">
                                                    {{ date('H:i', strtotime($jadwalItem->waktu_mulai)) }} - {{ date('H:i', strtotime($jadwalItem->waktu_selesai)) }}
                                                </small>
                                            @endif
                                            
                                            @if($jadwalItem)
                                                <div class="mt-2">
                                                    <button class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEdit"
                                                            @click="editData = {{ json_encode($jadwalItem) }}; editUrl = '{{ route('admin.jadwal.update', $jadwalItem->id) }}'">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalHapus"
                                                            @click="deleteUrl = '{{ route('admin.jadwal.destroy', $jadwalItem->id) }}'; deleteInfo = '{{ $hari }} - {{ $namaKelas }}'">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.jadwal.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} - {{ $kelas->kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">Pilih Guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_mulai" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_selesai" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="editUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" x-model="editData.hari" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" class="form-select" x-model="editData.kelas_id" required>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} - {{ $kelas->kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-select" x-model="editData.guru_id" required>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_mulai" class="form-control" x-model="editData.waktu_mulai" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_selesai" class="form-control" x-model="editData.waktu_selesai" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus jadwal <strong x-text="deleteInfo"></strong>?</p>
                        <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus Semua --}}
    <div class="modal fade" id="modalHapusSemua" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.jadwal.destroyAll') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Konfirmasi Hapus Semua Jadwal
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Peringatan!</strong> Tindakan ini akan menghapus SEMUA jadwal yang ada.
                        </div>
                        <p class="mb-2">Apakah Anda yakin ingin menghapus <strong>SEMUA jadwal mengajar</strong>?</p>
                        <ul class="text-muted small mb-0">
                            <li>Semua data jadwal akan dihapus permanen</li>
                            <li>Tindakan ini tidak dapat dibatalkan</li>
                            <li>Pastikan Anda sudah backup data jika diperlukan</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash-fill me-1"></i> Ya, Hapus Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection