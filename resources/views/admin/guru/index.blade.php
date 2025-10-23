@extends('layouts.app')

@section('title', 'Manajemen Guru')

@section('content')
<div class="container-fluid" x-data="manager()">
    @if (session('success'))
        {{-- Notifikasi Toast akan menangani ini --}}
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Guru</h5>
            <div class="d-flex align-items-center">
                <div class="input-group me-2">
                     <input type="search" class="form-control" placeholder="Cari Nama Guru..." x-model.debounce.300ms="searchQuery">
                </div>
                <button type="button" class="btn btn-outline-success flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                    <i class="bi bi-plus-lg"></i> Tambah Guru
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th @click="sortBy('nama')" style="cursor: pointer;">
                                Nama Guru
                                <span x-show="sortColumn === 'nama'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
                            </th>
                            <th @click="sortBy('jabatan')" style="cursor: pointer;">
                                Jabatan
                                <span x-show="sortColumn === 'jabatan'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
                            </th>
                            <th>Alamat</th>
                            <th>Pendidikan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in paginatedItems" :key="item.id">
                            <tr>
                                <td x-text="item.nama"></td>
                                <td x-text="item.jabatan"></td>
                                <td x-text="item.alamat"></td>
                                <td x-text="item.pendidikan"></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalEditGuru"
                                        @click="editData = item; editUrl = `/admin/guru/${item.id}`">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusGuru"
                                        @click="deleteName = item.nama_guru; deleteUrl = `/admin/guru/${item.id}`">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredItems.length === 0">
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
</div>


<div class="modal fade" id="modalTambahGuru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Guru Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-bold">Nama Guru</label><input type="text" class="form-control" name="nama" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Jabatan</label><input type="text" class="form-control" name="jabatan" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Alamat</label><textarea class="form-control" name="alamat" rows="3"></textarea></div>
                    <div class="mb-3"><label class="form-label fw-bold">Pendidikan</label><input type="text" class="form-control" name="pendidikan"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditGuru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h1 class="modal-title fs-5">Edit Data Guru</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form x-bind:action="editUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3"><label class="form-label fw-bold">Nama Guru</label><input type="text" class="form-control" name="nama" x-model="editData.nama" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Jabatan</label><input type="text" class="form-control" name="jabatan" x-model="editData.jabatan" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Alamat</label><textarea class="form-control" name="alamat" rows="3" x-model="editData.alamat"></textarea></div>
                    <div class="mb-3"><label class="form-label fw-bold">Pendidikan</label><input type="text" class="form-control" name="pendidikan" x-model="editData.pendidikan"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusGuru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Apakah Anda yakin ingin menghapus murid bernama <strong x-text="deleteName"></strong>?</p></div>
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


<script>
    function manager() {
        return {
            searchQuery: '',
            deleteName: '',
            deleteUrl: '',
            editUrl: '',
            editData: {},
            items: @json($guru),
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1, // <-- DITAMBAHKAN
            itemsPerPage: 5, // <-- DITAMBAHKAN

            sortBy(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            },

            get filteredItems() {
                let filtered = [...this.items];
                if (this.searchQuery) {
                    filtered = filtered.filter(item =>
                        item.nama.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                }
                if (this.sortColumn) {
                    filtered.sort((a, b) => {
                        const valA = a[this.sortColumn] || '';
                        const valB = b[this.sortColumn] || '';
                        return this.sortDirection === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    });
                }
                return filtered;
            },
            
            // LOGIKA PAGINATION DITAMBAHKAN DI SINI
            get totalPages() {
                return Math.ceil(this.filteredItems.length / this.itemsPerPage);
            },
            get paginatedItems() {
                if (this.totalPages > 0 && this.currentPage > this.totalPages) {
                    this.currentPage = 1;
                }
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredItems.slice(start, end);
            }
        }
    }
</script>
@endsection