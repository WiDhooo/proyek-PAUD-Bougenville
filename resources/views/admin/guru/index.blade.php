@extends('layouts.app')

@section('title', 'Manajemen Guru')

@section('content')
{{-- WRAPPER UTAMA: x-data memegang semua state --}}
<div class="container-fluid" x-data="guruManager()">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: var(--paud-radius-sm); border:none;" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="paud-card">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold mb-0" style="color: var(--paud-text);">
                    <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">
                        <i class="bi bi-person-badge-fill me-2" style="color: var(--paud-teal);"></i>Data Guru
                    </span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group" style="max-width: 240px;">
                        <span class="input-group-text" style="border-color: var(--paud-border); background: var(--paud-teal-light); border-radius: var(--paud-radius-sm) 0 0 var(--paud-radius-sm);">
                            <i class="bi bi-search" style="color: var(--paud-muted);"></i>
                        </span>
                        <input type="search" class="form-control" placeholder="Cari nama atau email..." x-model.debounce.300ms="searchQuery"
                               style="border-radius: 0 var(--paud-radius-sm) var(--paud-radius-sm) 0;">
                    </div>
                    <button type="button" class="btn paud-btn-primary btn-sm flex-shrink-0"
                            data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Guru
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="paud-thead">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th @click="sortBy('nama')" style="cursor:pointer; user-select:none;">
                                Nama Guru
                                <i class="bi" :class="sortColumn==='nama' ? (sortDirection==='asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'"></i>
                            </th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th @click="sortBy('jabatan')" style="cursor:pointer; user-select:none;">
                                Jabatan
                                <i class="bi" :class="sortColumn==='jabatan' ? (sortDirection==='asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'"></i>
                            </th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr class="paud-table-row">
                                <td class="fw-semibold" style="color: var(--paud-muted); font-size:0.82rem;" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="fw-semibold" style="color: var(--paud-text);" x-text="item.nama"></td>
                                <td style="color: var(--paud-muted); font-size:0.88rem;" x-text="item.email"></td>
                                <td style="color: var(--paud-muted); font-size:0.88rem;" x-text="item.no_hp"></td>
                                <td>
                                    <span class="paud-badge bg-paud-teal-light text-paud-teal" x-text="item.jabatan"></span>
                                </td>
                                <td style="text-align:center;">
                                    <button type="button"
                                        class="btn btn-sm me-1"
                                        style="border: 1.5px solid var(--paud-amber); color: var(--paud-amber); border-radius: 6px;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditGuru"
                                        @click="prepareEdit(item)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm"
                                        style="border: 1.5px solid var(--paud-coral); color: var(--paud-coral); border-radius: 6px;"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusGuru"
                                        @click="prepareDelete(item)" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="6" class="text-center py-4" style="color: var(--paud-muted);">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i> Data tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav x-show="totalPages > 1" class="d-flex justify-content-end mt-3">
                <ul class="pagination" style="--bs-pagination-active-bg: var(--paud-teal); --bs-pagination-active-border-color: var(--paud-teal);">
                    <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                        <a class="page-link" href="#" @click.prevent="currentPage--">‹</a>
                    </li>
                    <template x-for="page in totalPages" :key="page">
                        <li class="page-item" :class="{ 'active': currentPage === page }">
                            <a class="page-link" href="#" @click.prevent="currentPage = page" x-text="page"></a>
                        </li>
                    </template>
                    <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                        <a class="page-link" href="#" @click.prevent="currentPage++">›</a>
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
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2" style="color:var(--paud-teal);"></i>Tambah Guru Baru</h5>
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
                            <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn paud-btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Simpan</button>
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
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2" style="color:var(--paud-amber);"></i>Edit Data Guru</h5>
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
                            <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn paud-btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Simpan Perubahan</button>
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
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-danger btn-sm"><i class="bi bi-trash me-1"></i> Ya, Hapus</button>
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