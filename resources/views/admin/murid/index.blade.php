@extends('layouts.app')

@section('title', 'Manajemen Murid')

@section('content')
<div class="container-fluid" x-data="manager()">
    @if (session('success'))
        {{-- Notifikasi Toast akan menangani ini --}}
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Murid</h5>
            <div class="d-flex align-items-center">
                <div class="input-group me-2">
                     <input type="search" class="form-control" placeholder="Cari Nama Murid..." x-model.debounce.300ms="searchQuery">
                     <button class="btn btn-primary" type="button">Cari</button>
                </div>
                <button type="button" class="btn btn-outline-success flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalTambahMurid">
                    <i class="bi bi-plus-lg"></i> Tambah Murid
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th @click="sortBy('nama')" style="cursor: pointer;">
                                Nama Murid
                                <span x-show="sortColumn === 'nama'">
                                    <i :class="sortDirection === 'asc' ? 'bi bi-arrow-up' : 'bi-arrow-down'"></i>
                                </span>
                            </th>
                            <th>Usia</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr>
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td x-text="item.nik"></td>
                                <td x-text="item.nama"></td>
                                <td x-text="item.usia"></td>
                                <td x-text="item.jenis_kelamin"></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalEditMurid"
                                        @click="editData = item; editUrl = `/admin/murid/${item.id}`">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusMurid"
                                        @click="deleteName = item.nama; deleteUrl = `/admin/murid/${item.id}`">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                         <tr x-show="filteredItems.length === 0">
                            <td colspan="6" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav x-show="totalPages > 1" class="d-flex justify-content-end mt-3">
                <ul class="pagination">
                    <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                        <a class="page-link" href="#" @click.prevent="currentPage--">Previous</a>
                    </li>
                    <template x-for="page in totalPages" :key="page">
                        <li class="page-item" :class="{ 'active': currentPage === page }">
                            <a class="page-link" href="#" @click.prevent="currentPage = page" x-text="page"></a>
                        </li>
                    </template>
                    <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                        <a class="page-link" href="#" @click.prevent="currentPage++">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahMurid" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Murid Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="{{ route('admin.murid.store') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label class="form-label">NIK</label><input type="text" class="form-control" name="nik" required></div>
                    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" name="nama" required></div>
                    <div class="mb-3"><label class="form-label">Usia</label><input type="text" class="form-control" name="usia" required></div>
                    <div class="mb-3"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditMurid" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Murid</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form x-bind:action="editUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3"><label class="form-label">NIK</label><input type="text" class="form-control" name="nik" x-model="editData.nik" required></div>
                    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" name="nama" x-model="editData.nama" required></div>
                    <div class="mb-3"><label class="form-label">Usia</label><input type="text" class="form-control" name="usia" x-model="editData.usia" required></div>
                    <div class="mb-3"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select" x-model="editData.jenis_kelamin" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusMurid" tabindex="-1">
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
            items: @json($murid),
            currentPage: 1,
            itemsPerPage: 5,
            sortColumn: '',
            sortDirection: 'asc',

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
                        const valA = a[this.sortColumn] ? a[this.sortColumn] : '';
                        const valB = b[this.sortColumn] ? b[this.sortColumn] : '';
                        if (this.sortDirection === 'asc') {
                            return valA.localeCompare(valB);
                        } else {
                            return valB.localeCompare(valA);
                        }
                    });
                }
                return filtered;
            },
            
            get totalPages() {
                return Math.ceil(this.filteredItems.length / this.itemsPerPage);
            },

            get paginatedItems() {
                // Reset ke halaman pertama setiap kali filter atau sort berubah
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