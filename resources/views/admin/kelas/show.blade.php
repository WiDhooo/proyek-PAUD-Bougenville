@extends('layouts.app')

@section('title', 'Detail Kelas ' . $kelas['nama_kelas'])

@section('content')
<div class="container-fluid" x-data="classDetailManager()">
    <!-- Pesan Sukses -->
    @if (session('success'))
        {{-- Notifikasi Toast akan menangani ini --}}
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
                <div class="d-flex align-items-center gap-2">
                    <label for="filterGender" class="form-label mb-0">Filter:</label>
                    <select class="form-select form-select-sm" id="filterGender" x-model="filterGender" style="width: 200px;">
                        <option value="semua">Semua Jenis Kelamin</option>
                        <option value="Laki-Laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="d-flex">
                    <div class="input-group me-2">
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
                            {{-- KOLOM NAMA SEKARANG BISA DI-SORT --}}
                            <th @click="sortBy('nama')" style="cursor: pointer;">
                                Nama Murid
                                <span x-show="sortColumn === 'nama'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
                            </th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- LOOPING SEKARANG MENGGUNAKAN PAGINATED ITEMS --}}
                        <template x-for="(murid, index) in paginatedItems" :key="murid.id">
                            <tr>
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
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
                                        <i class="bi bi-trash-fill"></i>
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

            <!-- BLOK PAGINATION DITAMBAHKAN DI SINI -->
            <nav x-show="totalPages > 1" class="d-flex justify-content-end mt-3">
                <ul class="pagination">
                    <li class="page-item" :class="{ 'disabled': currentPage === 1 }"><a class="page-link" href="#" @click.prevent="currentPage--">Previous</a></li>
                    <template x-for="page in totalPages" :key="page">
                        <li class="page-item" :class="{ 'active': currentPage === page }"><a class="page-link" href="#" @click.prevent="currentPage = page" x-text="page"></a></li>
                    </template>
                    <li class="page-item" :class="{ 'disabled': currentPage === totalPages }"><a class="page-link" href="#" @click.prevent="currentPage++">Next</a></li>
                </ul>
            </nav>

        </div>
    </div>

    {{-- KODE MODAL LAMA ANDA --}}
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
                                            {{ $murid->nama }} (NIS: {{ $murid->nis }})
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
            deleteName: '',
            deleteUrl: '',
            filterGender: 'semua',
            searchQuery: '',

            // VARIABEL BARU UNTUK SORT & PAGINATION
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1,
            itemsPerPage: 5, // Ubah angka ini sesuai kebutuhan

            studentsInClass: @json($murid_di_kelas),

            sortBy(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            },
            
            get filteredStudents() {
                let students = [...this.studentsInClass];

                if (this.searchQuery) {
                    students = students.filter(
                        student => student.nama.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }

                if (this.filterGender !== 'semua') {
                    students = students.filter(
                        student => student.jenis_kelamin === this.filterGender
                    );
                }
                
                if (this.sortColumn) {
                    students.sort((a, b) => {
                        const valA = a[this.sortColumn] || '';
                        const valB = b[this.sortColumn] || '';
                        return this.sortDirection === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    });
                }
                
                return students;
            },

            // LOGIKA PAGINATION DITAMBAHKAN
            get totalPages() {
                return Math.ceil(this.filteredStudents.length / this.itemsPerPage);
            },
            get paginatedItems() {
                if (this.totalPages > 0 && this.currentPage > this.totalPages) {
                    this.currentPage = 1;
                }
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredStudents.slice(start, end);
            }
        }
    }
</script>
@endsection