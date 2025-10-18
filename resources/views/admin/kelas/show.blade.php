@extends('layouts.app')

@section('title', 'Detail Kelas ' . $kelas['nama_kelas'])

@section('content')
<div class="container-fluid" x-data="classDetailManager()">
    <!-- Pesan Sukses -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h3 class="fw-bold d-inline-block ms-3 mb-0">Kelas {{ $kelas['nama_kelas'] }} - {{ $kelas['kelas'] }}</h3>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <!-- Header Card: Filter & Tombol Aksi -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Bagian Filter --}}
                <div class="d-flex align-items-center gap-2">
                    <label for="filterGender" class="form-label mb-0">Filter:</label>
                    <select class="form-select form-select-sm" id="filterGender" x-model="filterGender" style="width: 200px;">
                        <option value="semua">Semua Jenis Kelamin</option>
                        <option value="Laki-Laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                {{-- Bagian Pencarian dan Tombol Tambah --}}
                <div class="d-flex">
                    <div class="me-2">
                        <input class="form-control" type="search" placeholder="Cari nama murid..." x-model.debounce.300ms="searchQuery">
                    </div>
                    <button type="button" class="btn btn-outline-success flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalTambahMuridKeKelas">
                        <i class="bi bi-plus-lg"></i> Tambah Murid
                    </button>
                </div>
            </div>

            <!-- Tabel Murid di Kelas -->
            <div class="table-responsive">
                <table class="table table-hover">
                     <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Murid</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(murid, index) in filteredStudents" :key="murid.id">
                            <tr>
                                <td x-text="index + 1"></td>
                                <td x-text="murid.nis"></td>
                                <td x-text="murid.nama"></td>
                                <td x-text="murid.jenis_kelamin"></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapusMurid"
                                        @click="
                                            deleteName = murid.nama;
                                            deleteUrl = `/admin/kelas/{{ $kelas['id'] }}/unassign-murid/${murid.id}`;
                                        ">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredStudents.length === 0">
                            <td colspan="5" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KODE MODAL TETAP SAMA --}}
    <!-- Modal Tambah Murid ke Kelas -->
    <div class="modal fade" id="modalTambahMuridKeKelas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Murid ke Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form action="{{ route('admin.kelas.assign', $kelas['id']) }}" method="POST">
                        @csrf
                        <div class="row">
                            @forelse ($semua_murid as $murid)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="murid_ids[]" value="{{ $murid['id'] }}" id="murid-{{ $murid['id'] }}">
                                        <label class="form-check-label" for="murid-{{ $murid['id'] }}">
                                            {{ $murid['nama'] }} (NIK: {{ $murid['nik'] }})
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center">Semua murid sudah terdaftar di kelas lain.</p>
                            @endforelse
                        </div>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Konfirmasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Murid dari Kelas -->
    <div class="modal fade" id="modalHapusMurid" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><p>Apakah Anda yakin ingin menghapus <strong x-text="deleteName"></strong> dari kelas ini?</p></div>
                <div class="modal-footer">
                    <form x-bind:action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function classDetailManager() {
        return {
            // Variabel untuk modal
            deleteName: '',
            deleteUrl: '',
            
            // Variabel untuk filter & search
            filterGender: 'semua',
            searchQuery: '', // <-- Tambahkan ini

            // Data mentah dari Laravel
            studentsInClass: @json($murid_di_kelas),
            
            // Logika untuk menampilkan data yang sudah difilter dan dicari
            get filteredStudents() {
                let students = this.studentsInClass;

                // 1. Terapkan filter PENCARIAN
                if (this.searchQuery) {
                    students = students.filter(
                        student => student.nama.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }

                // 2. Terapkan filter JENIS KELAMIN
                if (this.filterGender !== 'semua') {
                    students = students.filter(
                        student => student.jenis_kelamin === this.filterGender
                    );
                }
                
                return students;
            }
        }
    }
</script>
@endsection