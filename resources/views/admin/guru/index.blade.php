@extends('layouts.app')

@section('title', 'Manajemen Guru')

@section('content')
{{-- WRAPPER UTAMA: x-data memegang semua state --}}
<div class="container-fluid" x-data="guruManager()">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
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
                            <th>No</th>
                            <th @click="sortBy('nama')" style="cursor: pointer;">
                                Nama Guru
                                <span x-show="sortColumn === 'nama'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
                            </th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th @click="sortBy('jabatan')" style="cursor: pointer;">
                                Jabatan
                                <span x-show="sortColumn === 'jabatan'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr>
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td x-text="item.nama"></td>
                                <td x-text="item.email"></td>
                                <td x-text="item.no_hp"></td>
                                <td x-text="item.jabatan"></td>
                                <td>
                                    {{-- TOMBOL EDIT --}}
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalEditGuru"
                                        @click="prepareEdit(item)">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    {{-- TOMBOL HAPUS --}}
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusGuru"
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

    {{-- ================================================================================= --}}
    {{-- MODAL TAMBAH GURU (Validasi Lengkap) --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalTambahGuru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.guru.store') }}" method="POST" @submit="submitAdd($event)">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Tambah Guru Baru</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- Nama --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" 
                                    x-model="addData.nama" :class="{'is-invalid': addErrors.nama}" @input="validateAdd('nama')">
                                <div class="invalid-feedback" x-text="addErrors.nama"></div>
                            </div>
                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" 
                                    x-model="addData.email" :class="{'is-invalid': addErrors.email}" @input="validateAdd('email')">
                                <div class="invalid-feedback" x-text="addErrors.email"></div>
                            </div>
                        </div>
                        
                        {{-- Password Section --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" 
                                    x-model="addData.password" :class="{'is-invalid': addErrors.password}" @input="validateAdd('password')">
                                <div class="invalid-feedback" x-text="addErrors.password"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" 
                                    x-model="addData.password_confirmation" :class="{'is-invalid': addErrors.password_confirmation}" @input="validateAdd('password_confirmation')">
                                <div class="invalid-feedback" x-text="addErrors.password_confirmation"></div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            {{-- TTL (Opsional di controller, tapi kita validasi ringan) --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir" x-model="addData.tempat_lahir">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir" x-model="addData.tanggal_lahir">
                            </div>
                        </div>
                        <div class="row">
                            {{-- No HP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_hp" 
                                    x-model="addData.no_hp" :class="{'is-invalid': addErrors.no_hp}" @input="validateAdd('no_hp')">
                                <div class="invalid-feedback" x-text="addErrors.no_hp"></div>
                            </div>
                            {{-- Jabatan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan" class="form-control" 
                                    x-model="addData.jabatan" :class="{'is-invalid': addErrors.jabatan}" @change="validateAdd('jabatan')">
                                    <option value="" disabled>Pilih jabatan</option>
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    <option value="Sekretaris">Sekretaris</option>
                                    <option value="Bendahara">Bendahara</option>
                                    <option value="Pendidik">Pendidik</option>
                                </select>
                                <div class="invalid-feedback" x-text="addErrors.jabatan"></div>
                            </div>
                        </div>
                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alamat" rows="3" 
                                x-model="addData.alamat" :class="{'is-invalid': addErrors.alamat}" @input="validateAdd('alamat')"></textarea>
                            <div class="invalid-feedback" x-text="addErrors.alamat"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================================= --}}
    {{-- MODAL EDIT GURU (Validasi, Password Opsional) --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalEditGuru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form :action="editUrl" method="POST" @submit="submitEdit($event)">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Edit Data Guru</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- Nama --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" 
                                    x-model="editData.nama" :class="{'is-invalid': editErrors.nama}" @input="validateEdit('nama')">
                                <div class="invalid-feedback" x-text="editErrors.nama"></div>
                            </div>
                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" 
                                    x-model="editData.email" :class="{'is-invalid': editErrors.email}" @input="validateEdit('email')">
                                <div class="invalid-feedback" x-text="editErrors.email"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" class="form-control" name="password" 
                                       x-model="editData.password" @input="validateEdit('password')"
                                       placeholder="Kosongkan jika tidak ingin diubah">
                                <small class="text-danger" x-show="editErrors.password" x-text="editErrors.password"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Konfirmasi Password</label>
                                <input type="password" class="form-control" name="password_confirmation" 
                                       x-model="editData.password_confirmation" @input="validateEdit('password')"
                                       placeholder="Kosongkan jika tidak ingin diubah">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir" x-model="editData.tempat_lahir">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir" x-model="editData.tanggal_lahir">
                            </div>
                        </div>
                        <div class="row">
                            {{-- HP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_hp" 
                                    x-model="editData.no_hp" :class="{'is-invalid': editErrors.no_hp}" @input="validateEdit('no_hp')">
                                <div class="invalid-feedback" x-text="editErrors.no_hp"></div>
                            </div>
                            {{-- Jabatan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan" class="form-control" 
                                    x-model="editData.jabatan" :class="{'is-invalid': editErrors.jabatan}" @change="validateEdit('jabatan')">
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    <option value="Sekretaris">Sekretaris</option>
                                    <option value="Bendahara">Bendahara</option>
                                    <option value="Pendidik">Pendidik</option>
                                </select>
                                <div class="invalid-feedback" x-text="editErrors.jabatan"></div>
                            </div>
                        </div>
                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alamat" rows="3" 
                                x-model="editData.alamat" :class="{'is-invalid': editErrors.alamat}" @input="validateEdit('alamat')"></textarea>
                            <div class="invalid-feedback" x-text="editErrors.alamat"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================================= --}}
    {{-- MODAL HAPUS GURU --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalHapusGuru" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus guru bernama <strong x-text="deleteData.nama"></strong>?</p>
                    <p class="text-danger small mb-0">PERINGATAN: Akun login user terkait juga akan dihapus permanen.</p>
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

</div> {{-- END CONTAINER --}}

<script>
    function guruManager() {
        return {
            items: @json($gurus),
            searchQuery: '',
            
            // Pagination & Sort
            currentPage: 1,
            itemsPerPage: 5,
            sortColumn: '',
            sortDirection: 'asc',

            // --- STATE TAMBAH ---
            addData: {
                nama: '', email: '', password: '', password_confirmation: '',
                tempat_lahir: '', tanggal_lahir: '', no_hp: '', alamat: '', jabatan: ''
            },
            addErrors: {
                nama: '', email: '', password: '', password_confirmation: '',
                no_hp: '', alamat: '', jabatan: ''
            },

            // --- STATE EDIT ---
            editUrl: '',
            editData: {
                id: null, nama: '', email: '', password: '', password_confirmation: '',
                tempat_lahir: '', tanggal_lahir: '', no_hp: '', alamat: '', jabatan: ''
            },
            editErrors: {
                nama: '', email: '', password: '', no_hp: '', alamat: '', jabatan: ''
            },

            // --- STATE HAPUS ---
            deleteUrl: '',
            deleteData: { nama: '' },

            // --- LOGIKA VALIDASI TAMBAH ---
            validateAdd(field) {
                const data = this.addData;
                const err = this.addErrors;

                if(field === 'nama') err.nama = data.nama ? '' : 'Nama Lengkap wajib diisi.';
                
                if(field === 'email') {
                    if(!data.email) err.email = 'Email wajib diisi.';
                    else if(!/\S+@\S+\.\S+/.test(data.email)) err.email = 'Format email tidak valid.';
                    else err.email = '';
                }

                if(field === 'password' || field === 'password_confirmation') {
                    if(!data.password) err.password = 'Password wajib diisi.';
                    else if(data.password.length < 8) err.password = 'Password minimal 8 karakter.';
                    else err.password = '';

                    if(data.password !== data.password_confirmation) err.password_confirmation = 'Konfirmasi password tidak cocok.';
                    else err.password_confirmation = '';
                }

                if(field === 'no_hp') err.no_hp = data.no_hp ? '' : 'Nomor HP wajib diisi.';
                if(field === 'alamat') err.alamat = data.alamat ? '' : 'Alamat wajib diisi.';
                if(field === 'jabatan') err.jabatan = data.jabatan ? '' : 'Jabatan wajib dipilih.';
            },

            submitAdd(e) {
                // Trigger semua validasi
                ['nama', 'email', 'password', 'password_confirmation', 'no_hp', 'alamat', 'jabatan'].forEach(f => this.validateAdd(f));
                
                // Cek jika ada error string yang tidak kosong
                const hasError = Object.values(this.addErrors).some(val => val !== '');
                if (hasError) e.preventDefault();
            },


            // --- LOGIKA VALIDASI EDIT ---
            prepareEdit(item) {
                this.editData = {
                    id: item.id,
                    nama: item.nama,
                    email: item.email,
                    password: '', // Password kosong default saat edit
                    password_confirmation: '',
                    tempat_lahir: item.tempat_lahir,
                    // Handle format tanggal YYYY-MM-DD
                    tanggal_lahir: item.tanggal_lahir ? String(item.tanggal_lahir).substring(0, 10) : '',
                    no_hp: item.no_hp,
                    alamat: item.alamat,
                    jabatan: item.jabatan
                };
                this.editUrl = `/admin/guru/${item.id}`;
                
                // Reset errors
                this.editErrors = { nama: '', email: '', password: '', no_hp: '', alamat: '', jabatan: '' };
            },

            validateEdit(field) {
                const data = this.editData;
                const err = this.editErrors;

                if(field === 'nama') err.nama = data.nama ? '' : 'Nama Lengkap wajib diisi.';
                
                if(field === 'email') {
                    if(!data.email) err.email = 'Email wajib diisi.';
                    else if(!/\S+@\S+\.\S+/.test(data.email)) err.email = 'Format email tidak valid.';
                    else err.email = '';
                }

                // Password Opsional saat Edit
                if(field === 'password') {
                    if(data.password && data.password.length < 8) {
                        err.password = 'Password baru minimal 8 karakter.';
                    } else if (data.password && data.password !== data.password_confirmation) {
                        err.password = 'Konfirmasi password tidak cocok.';
                    } else {
                        err.password = '';
                    }
                }

                if(field === 'no_hp') err.no_hp = data.no_hp ? '' : 'Nomor HP wajib diisi.';
                if(field === 'alamat') err.alamat = data.alamat ? '' : 'Alamat wajib diisi.';
                if(field === 'jabatan') err.jabatan = data.jabatan ? '' : 'Jabatan wajib dipilih.';
            },

            submitEdit(e) {
                // Validasi manual field wajib
                this.validateEdit('nama');
                this.validateEdit('email');
                this.validateEdit('no_hp');
                this.validateEdit('alamat');
                this.validateEdit('jabatan');
                
                // Jika password diisi, validasi password juga
                if(this.editData.password) this.validateEdit('password');

                const hasError = Object.values(this.editErrors).some(val => val !== '');
                if (hasError) e.preventDefault();
            },

            // --- LOGIKA HAPUS ---
            prepareDelete(item) {
                this.deleteData = item;
                this.deleteUrl = `/admin/guru/${item.id}`;
            },

            // --- SEARCH & SORT & PAGINATION (Logic Standar) ---
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
                    const lower = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(item =>
                        item.nama.toLowerCase().includes(lower) ||
                        item.email.toLowerCase().includes(lower)
                    );
                }
                if (this.sortColumn) {
                    filtered.sort((a, b) => {
                        const valA = a[this.sortColumn] || '', valB = b[this.sortColumn] || '';
                        return this.sortDirection === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    });
                }
                return filtered;
            },
            get totalPages() { return Math.ceil(this.filteredItems.length / this.itemsPerPage); },
            get paginatedItems() {
                if (this.totalPages > 0 && this.currentPage > this.totalPages) this.currentPage = 1;
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredItems.slice(start, start + this.itemsPerPage);
            }
        }
    }
</script>
@endsection