@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
{{-- Container UTAMA: Semua Modal HARUS di dalam div ini agar terbaca --}}
<div class="container-fluid" x-data="studentManager()">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
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
                                    {{-- TOMBOL EDIT --}}
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalEditSiswa"
                                        @click="prepareEdit(item)">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    
                                    {{-- TOMBOL HAPUS --}}
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusSiswa"
                                        @click="prepareDelete(item)">
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

    {{-- ================================================================================= --}}
    {{-- MODAL TAMBAH SISWA (Dengan Validasi) --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalTambahSiswa" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.siswa.store') }}" method="POST" @submit="submitAdd($event)">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Tambah Siswa Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" class="form-control" name="nis" 
                                x-model="addData.nis" 
                                :class="{'is-invalid': addErrors.nis}" 
                                @input="validateAdd('nis')">
                            <div class="invalid-feedback" x-text="addErrors.nis"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" 
                                x-model="addData.nama" 
                                :class="{'is-invalid': addErrors.nama}" 
                                @input="validateAdd('nama')">
                            <div class="invalid-feedback" x-text="addErrors.nama"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" 
                                x-model="addData.tanggal_lahir" 
                                :class="{'is-invalid': addErrors.tanggal_lahir}" 
                                @change="validateAdd('tanggal_lahir')">
                            <div class="invalid-feedback" x-text="addErrors.tanggal_lahir"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" 
                                x-model="addData.jenis_kelamin" 
                                :class="{'is-invalid': addErrors.jenis_kelamin}" 
                                @change="validateAdd('jenis_kelamin')">
                                <option value="" disabled selected>Pilih jenis kelamin</option>
                                <option value="Laki-Laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback" x-text="addErrors.jenis_kelamin"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================================= --}}
    {{-- MODAL EDIT SISWA (Dengan Validasi) --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalEditSiswa" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form :action="editUrl" method="POST" @submit="submitEdit($event)">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" class="form-control" name="nis" 
                                x-model="editData.nis"
                                :class="{'is-invalid': editErrors.nis}"
                                @input="validateEdit('nis')">
                            <div class="invalid-feedback" x-text="editErrors.nis"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" 
                                x-model="editData.nama"
                                :class="{'is-invalid': editErrors.nama}"
                                @input="validateEdit('nama')">
                            <div class="invalid-feedback" x-text="editErrors.nama"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" 
                                x-model="editData.tanggal_lahir"
                                :class="{'is-invalid': editErrors.tanggal_lahir}"
                                @change="validateEdit('tanggal_lahir')">
                            <div class="invalid-feedback" x-text="editErrors.tanggal_lahir"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" 
                                x-model="editData.jenis_kelamin"
                                :class="{'is-invalid': editErrors.jenis_kelamin}"
                                @change="validateEdit('jenis_kelamin')">
                                <option value="Laki-Laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback" x-text="editErrors.jenis_kelamin"></div>
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

    {{-- ================================================================================= --}}
    {{-- MODAL HAPUS SISWA (Standar Konfirmasi) --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalHapusSiswa" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data siswa <strong><span x-text="deleteTarget.nama"></span></strong>?</p>
                    <p class="text-danger small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div> {{-- Penutup DIV CONTAINER UTAMA --}}

<script>
    function studentManager() {
        return {
            // DATA UTAMA
            items: @json($siswa), // Pastikan controller mengirim compact('siswa')
            searchQuery: '',
            
            // PAGINATION & SORT
            currentPage: 1,
            itemsPerPage: 5,
            sortColumn: '',
            sortDirection: 'asc',

            // STATE UNTUK TAMBAH
            addData: { nis: '', nama: '', tanggal_lahir: '', jenis_kelamin: '' },
            addErrors: { nis: '', nama: '', tanggal_lahir: '', jenis_kelamin: '' },

            // STATE UNTUK EDIT
            editUrl: '',
            editData: { id: null, nis: '', nama: '', tanggal_lahir: '', jenis_kelamin: '' },
            editErrors: { nis: '', nama: '', tanggal_lahir: '', jenis_kelamin: '' },

            // STATE UNTUK DELETE
            deleteUrl: '',
            deleteTarget: { nama: '' },

            // --- HELPER: Cek Umur (Minimal 2 Tahun) ---
            checkAge(dateString) {
                if (!dateString) return true; // Biarkan kosong ditangani validasi 'required'
                const inputDate = new Date(dateString);
                const today = new Date();
                
                // Hitung tanggal 2 tahun yang lalu dari hari ini
                const minDate = new Date();
                minDate.setFullYear(today.getFullYear() - 2);

                // Jika inputDate lebih besar dari minDate, berarti belum 2 tahun
                return inputDate < minDate; 
            },

            // --- FUNGSI LOGIKA TAMBAH ---
            validateAdd(field) {
                const data = this.addData;
                const err = this.addErrors;

                // Validasi NIS (Required, Numeric, Unique Check Client-side)
                if(field === 'nis') {
                    if (!data.nis) {
                        err.nis = 'NIS wajib diisi';
                    } else if (!/^\d+$/.test(data.nis)) {
                        err.nis = 'NIS harus berupa angka.';
                    } else {
                        // Cek duplikat di data yang sudah diload (Client Side Check)
                        const isDuplicate = this.items.some(item => String(item.nis) === String(data.nis));
                        err.nis = isDuplicate ? 'NIS sudah terdaftar (Cek data tabel).' : '';
                    }
                }

                if(field === 'nama') err.nama = data.nama ? '' : 'Nama wajib diisi';
                
                // Validasi Tanggal Lahir (Minimal 2 Tahun)
                if(field === 'tanggal_lahir') {
                    if (!data.tanggal_lahir) {
                        err.tanggal_lahir = 'Tanggal lahir wajib diisi';
                    } else if (!this.checkAge(data.tanggal_lahir)) {
                        err.tanggal_lahir = 'Usia siswa minimal 2 tahun.';
                    } else {
                        err.tanggal_lahir = '';
                    }
                }

                if(field === 'jenis_kelamin') err.jenis_kelamin = data.jenis_kelamin ? '' : 'Pilih jenis kelamin';
            },

            submitAdd(e) {
                // Trigger semua validasi sebelum submit
                ['nis', 'nama', 'tanggal_lahir', 'jenis_kelamin'].forEach(f => this.validateAdd(f));
                
                // Cek apakah ada error
                if (Object.values(this.addErrors).some(msg => msg !== '')) {
                    e.preventDefault();
                }
            },

            // --- FUNGSI LOGIKA EDIT ---
            prepareEdit(item) {
                this.editData = {
                    id: item.id,
                    nis: item.nis,
                    nama: item.nama,
                    tanggal_lahir: item.tanggal_lahir ? String(item.tanggal_lahir).substring(0, 10) : '',
                    jenis_kelamin: item.jenis_kelamin
                };
                this.editUrl = `/admin/siswa/${item.id}`;
                this.editErrors = { nis: '', nama: '', tanggal_lahir: '', jenis_kelamin: '' };
            },
            
            validateEdit(field) {
                const data = this.editData;
                const err = this.editErrors;

                // Validasi NIS Edit
                if(field === 'nis') {
                    if (!data.nis) {
                        err.nis = 'NIS wajib diisi';
                    } else if (!/^\d+$/.test(data.nis)) {
                        err.nis = 'NIS harus berupa angka.';
                    } else {
                        // Cek duplikat (kecuali punya sendiri)
                        const isDuplicate = this.items.some(item => 
                            String(item.nis) === String(data.nis) && item.id !== data.id
                        );
                        err.nis = isDuplicate ? 'NIS sudah digunakan siswa lain.' : '';
                    }
                }

                if(field === 'nama') err.nama = data.nama ? '' : 'Nama wajib diisi';
                
                // Validasi Tanggal Lahir Edit
                if(field === 'tanggal_lahir') {
                    if (!data.tanggal_lahir) {
                        err.tanggal_lahir = 'Tanggal lahir wajib diisi';
                    } else if (!this.checkAge(data.tanggal_lahir)) {
                        err.tanggal_lahir = 'Usia siswa minimal 2 tahun.';
                    } else {
                        err.tanggal_lahir = '';
                    }
                }

                if(field === 'jenis_kelamin') err.jenis_kelamin = data.jenis_kelamin ? '' : 'Pilih jenis kelamin';
            },

            submitEdit(e) {
                ['nis', 'nama', 'tanggal_lahir', 'jenis_kelamin'].forEach(f => this.validateEdit(f));
                if (Object.values(this.editErrors).some(msg => msg !== '')) {
                    e.preventDefault();
                }
            },

            // --- FUNGSI LOGIKA HAPUS ---
            prepareDelete(item) {
                this.deleteTarget = item;
                this.deleteUrl = `/admin/siswa/${item.id}`;
            },

            // --- FUNGSI STANDAR TABEL ---
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
                    filtered = filtered.filter(item => item.nama.toLowerCase().includes(this.searchQuery.toLowerCase()));
                }
                if (this.sortColumn) {
                    filtered.sort((a, b) => {
                        let valA = a[this.sortColumn] || '', valB = b[this.sortColumn] || '';
                        // Penanganan khusus jika sort NIS agar urut angka (bukan string '10' < '2')
                        if(this.sortColumn === 'nis') {
                            return this.sortDirection === 'asc' ? valA - valB : valB - valA;
                        }
                        return this.sortDirection === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    });
                }
                return filtered;
            },
            get totalPages() { return Math.ceil(this.filteredItems.length / this.itemsPerPage); },
            get paginatedItems() {
                if (this.totalPages > 0 && this.currentPage > this.totalPages) this.currentPage = 1;
                let start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredItems.slice(start, start + this.itemsPerPage);
            }
        }
    }
</script>
@endsection