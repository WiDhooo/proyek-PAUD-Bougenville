@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
<div class="container-fluid" x-data="manager()">
    @if (session('success'))
        {{-- Notifikasi Toast akan menangani ini --}}
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Siswa</h5>
            <div class="d-flex align-items-center">
                <div class="input-group me-2">
                     <input type="search" class="form-control" placeholder="Cari Nama Siswa..." x-model.debounce.300ms="searchQuery">
                </div>
                <button type="button" class="btn btn-outline-success flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                    <i class="bi bi-plus-lg"></i> Tambah Siswa
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th @click="sortBy('nama')" style="cursor: pointer;">
                                Nama Siswa
                                <span x-show="sortColumn === 'nama'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
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
                                <td x-text="item.nis"></td>
                                <td x-text="item.nama"></td>
                                <td x-text="item.usia"></td>
                                <td x-text="item.jenis_kelamin"></td>
                                <td>
                                    {{-- PERUBAHAN 1: Tombol sekarang MENGIRIM EVENT --}}
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalEditSiswa"
                                        @click="$dispatch('open-edit-modal', { item: item })">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusSiswa"
                                        @click="$dispatch('open-hapus-modal', { item: item })">
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

{{-- Modal Tambah Siswa (Tidak Berubah) --}}
<div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Siswa Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="{{ route('admin.siswa.store') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label class="form-label">NIS</label><input type="text" class="form-control" name="nis" required></div>
                    <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" name="nama" required></div>
                    <div class="mb-3"><label class="form-label">Tanggal Lahir</label><input type="date" class="form-control" name="tanggal_lahir" required></div>
                    <div class="mb-3"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select" required><option value="" disabled selected>Pilih jenis kelamin</option><option value="Laki-Laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- PERUBAHAN 2: Modal Edit sekarang punya x-data dan event listener sendiri --}}
<div class="modal fade" id="modalEditSiswa" tabindex="-1" aria-labelledby="modalEditKelasLabel" aria-hidden="true"
     x-data="{ editUrl: '', editData: { id: null, nis: '', nama: '', tanggal_lahir: '', jenis_kelamin: '' } }"
     @open-edit-modal.window="
        let item = event.detail.item;
        editData.id = item.id;
        editData.nis = item.nis;
        editData.nama = item.nama;
        editData.tanggal_lahir = item.tanggal_lahir ? String(item.tanggal_lahir).substring(0, 10) : '';
        editData.jenis_kelamin = item.jenis_kelamin;
        editUrl = `/admin/siswa/${item.id}`;
     ">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form :action="editUrl" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditKelasLabel">Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="upd-nis" class="form-label">NIS</label>
                        <input type="text" class="form-control" name="nis" id="upd-nis" x-model="editData.nis" required>
                    </div>
                    <div class="mb-3">
                        <label for="upd-nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="upd-nama" name="nama" x-model="editData.nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="upd-tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="upd-tanggal_lahir" name="tanggal_lahir" x-model="editData.tanggal_lahir" required>
                    </div>
                    <div class="mb-3">
                        <label for="upd-jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="upd-jenis_kelamin" class="form-select" x-model="editData.jenis_kelamin" required>
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hapus Siswa (Tidak Berubah) --}}
<div class="modal fade" id="modalHapusSiswa" tabindex="-1" aria-labelledby="modalHapusKelasLabel" aria-hidden="true"
     x-data="{ hapusUrl: '', hapusData: { id: null, nis: '', nama: '', tanggal_lahir: '', jenis_kelamin: '' } }"
     @open-hapus-modal.window="
        let item = event.detail.item;
        hapusData.id = item.id;
        hapusData.nis = item.nis;
        hapusData.nama = item.nama;
        hapusData.tanggal_lahir = item.tanggal_lahir ? String(item.tanggal_lahir).substring(0, 10) : '';
        hapusData.jenis_kelamin = item.jenis_kelamin;
        hapusUrl = `/admin/siswa/${item.id}`;
     ">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Apakah Anda yakin ingin menghapus siswa bernama <strong x-text="hapusData.nama"></strong>?</p></div>
            <div class="modal-footer">
                <form :action="hapusUrl" method="POST">
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
            
            {{-- PERUBAHAN 3: editUrl dan editData DIHAPUS dari sini --}}
            
            items: @json($siswa),
            currentPage: 1,
            itemsPerPage: 5,
            sortColumn: '',
            sortDirection: 'asc',

            {{-- FUNGSI openEditModal DIHAPUS dari sini --}}

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