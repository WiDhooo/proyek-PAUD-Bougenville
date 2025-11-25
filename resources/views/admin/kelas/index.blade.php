@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="container-fluid" x-data="manager()">
    @if (session('success'))
        {{-- Notifikasi Toast akan menangani ini --}}
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Kelas</h5>
            <div class="d-flex align-items-center">
                <div class="input-group me-2">
                     <input type="search" class="form-control" placeholder="Cari Nama Kelas..." x-model.debounce.300ms="searchQuery">
                </div>
                <button type="button" class="btn btn-outline-success flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                    <i class="bi bi-plus-lg"></i> Buat Kelas
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th @click="sortBy('nama_kelas')" style="cursor: pointer;">
                                Nama Kelas
                                <span x-show="sortColumn === 'nama_kelas'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
                            </th>
                            <th>Kelas</th>
                            <th @click="sortBy('wali')" style="cursor: pointer;">
                                Wali
                                <span x-show="sortColumn === 'wali'"><i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i></span>
                            </th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                         <template x-for="item in paginatedItems" :key="item.id">
                            <tr>
                                <td x-text="item.nama_kelas"></td>
                                <td x-text="item.kelas"></td>
                                <td x-text="item.wali"></td>
                                <td>
                                    <a :href="`/admin/kelas/${item.id}`" class="btn btn-info btn-sm text-white"><i class="bi bi-info-circle-fill"></i> Detail</a>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalEditKelas"
                                        @click="editData = item; editUrl = `/admin/kelas/${item.id}`">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusKelas"
                                        @click="deleteName = item.nama_kelas; deleteUrl = `/admin/kelas/${item.id}`">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                         <tr x-show="filteredItems.length === 0">
                            <td colspan="4" class="text-center">Data tidak ditemukan.</td>
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

            <!-- Modal Tambah Kelas dengan Validasi -->
            <div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-labelledby="modalTambahKelasLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambahKelasLabel">Buat Kelas Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        {{-- Menambahkan x-data="classFormValidator()" untuk scope validasi --}}
                        <div class="modal-body" x-data="classFormValidator()">
                            <form action="{{ route('admin.kelas.store') }}" method="POST" @submit="submitForm($event)">
                                @csrf
                                
                                {{-- Validasi Nama Kelas --}}
                                <div class="mb-3">
                                    <label for="add-nama_kelas" class="form-label">Nama Kelas</label>
                                    <input type="text" class="form-control" id="add-nama_kelas" name="nama_kelas" 
                                           placeholder="Contoh: Mandiri, Kreatif, Ceria" 
                                           x-model="fields.nama_kelas" 
                                           @input="validate('nama_kelas')"
                                           :class="{'is-invalid': errors.nama_kelas}">
                                    <div class="invalid-feedback" x-text="errors.nama_kelas"></div>
                                </div>

                                {{-- Validasi Kelas (Tingkat) --}}
                                <div class="mb-3">
                                    <label for="add-kelas" class="form-label">Tingkat/Kelompok</label>
                                    <input type="text" class="form-control" id="add-kelas" name="kelas" 
                                           placeholder="Contoh: A, B (Max 1 Karakter)" 
                                           x-model="fields.kelas" 
                                           @input="validate('kelas')"
                                           :class="{'is-invalid': errors.kelas}">
                                    <div class="invalid-feedback" x-text="errors.kelas"></div>
                                    <small class="text-muted" x-show="!errors.kelas">Maksimal 1 karakter (misal: A atau B)</small>
                                </div>

                                {{-- Validasi Wali Kelas --}}
                                <div class="mb-3">
                                    <label for="add-wali_id" class="form-label">Wali Kelas</label>
                                    <select class="form-select" id="add-wali_id" name="guru_id" 
                                            x-model="fields.guru_id" 
                                            @change="validate('guru_id')"
                                            :class="{'is-invalid': errors.guru_id}">
                                        <option value="" disabled selected>Pilih seorang guru</option>
                                        @isset($gurus)
                                            @foreach($gurus as $guru)
                                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                            @endforeach
                                        @else
                                            <option disabled>Data guru tidak ditemukan</option>
                                        @endisset
                                    </select>
                                    <div class="invalid-feedback" x-text="errors.guru_id"></div>
                                </div>

                                <div class="modal-footer px-0 pb-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    {{-- Tombol disable jika ada error atau data kosong (opsional, tapi bagus untuk UX) --}}
                                    <button type="submit" class="btn btn-success">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalEditKelas" tabindex="-1" aria-labelledby="modalEditKelasLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">     
                        <form :action="editUrl" method="POST" @submit="submitEdit($event)">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditKelasLabel">Edit Kelas <span x-text="editData.nama_kelas"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                {{-- Input 1: Nama Kelas --}}
                                <div class="mb-3">
                                    <label for="upd-nama_kelas" class="form-label">Nama Kelas</label>
                                    <input type="text" class="form-control" id="upd-nama_kelas" name="nama_kelas" 
                                        x-model="editData.nama_kelas"
                                        @input="validateEdit('nama_kelas')"
                                        :class="{'is-invalid': editErrors.nama_kelas}"
                                        required>
                                    <div class="invalid-feedback" x-text="editErrors.nama_kelas"></div>
                                </div>
                                
                                {{-- Input 2: Tingkat/Kelompok (A/B) --}}
                                <div class="mb-3">
                                    <label for="upd-kelas" class="form-label">Tingkat/Kelompok</label>
                                    <input type="text" class="form-control" id="upd-kelas" name="kelas" 
                                        x-model="editData.kelas"
                                        @input="validateEdit('kelas')"
                                        :class="{'is-invalid': editErrors.kelas}" 
                                        placeholder="Maksimal 1 Karakter (misal: A)"
                                        required>
                                    <div class="invalid-feedback" x-text="editErrors.kelas"></div>
                                    <small class="text-muted" x-show="!editErrors.kelas">Maksimal 1 karakter (misal: A atau B)</small>
                                </div>
                                
                                {{-- Input 3: Wali Kelas (Dropdown) --}}
                                <div class="mb-3">
                                    <label for="upd-wali_id" class="form-label">Wali Kelas</label>
                                    <select class="form-select" id="upd-wali_id" name="guru_id" 
                                            x-model="editData.guru_id"
                                            @change="validateEdit('guru_id')"
                                            :class="{'is-invalid': editErrors.guru_id}" 
                                            required>
                                        @isset($gurus)
                                            @foreach($gurus as $guru)
                                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                            @endforeach
                                        @else
                                            <option disabled>Data guru tidak ditemukan</option>
                                        @endisset
                                    </select>
                                    <div class="invalid-feedback" x-text="editErrors.guru_id"></div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            </div>
                        </form>

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

            <div class="modal fade" id="modalHapusKelas" tabindex="-1" aria-labelledby="modalHapusKelasLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form :action="deleteUrl" method="POST">
                            @csrf
                            @method('DELETE')

                            <div class="modal-header">
                                <h5 class="modal-title" id="modalHapusKelasLabel">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <p>Anda yakin ingin menghapus data kelas: 
                                <strong x-text="deleteName"></strong>?
                                </p>
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
        </div>
    </div>
</div>

<script>
    // Fungsi untuk Validasi Form Tambah Kelas
    function classFormValidator() {
        return {
            fields: {
                nama_kelas: '',
                kelas: '',
                guru_id: ''
            },
            errors: {
                nama_kelas: '',
                kelas: '',
                guru_id: ''
            },
            validate(field) {
                // Validasi Nama Kelas
                if (field === 'nama_kelas') {
                    if (!this.fields.nama_kelas) {
                        this.errors.nama_kelas = 'Nama Kelas wajib diisi.';
                    } else if (this.fields.nama_kelas.length > 100) {
                        this.errors.nama_kelas = 'Maksimal 100 karakter.';
                    } else {
                        this.errors.nama_kelas = '';
                    }
                }
                
                // Validasi Tingkat Kelas (Sesuai Controller max:1)
                if (field === 'kelas') {
                    if (!this.fields.kelas) {
                        this.errors.kelas = 'Tingkat/Kelompok wajib diisi.';
                    } else if (this.fields.kelas.length > 1) {
                        this.errors.kelas = 'Maksimal 1 karakter (Contoh: A).';
                    } else {
                        this.errors.kelas = '';
                    }
                }

                // Validasi Wali Kelas
                if (field === 'guru_id') {
                    if (!this.fields.guru_id) {
                        this.errors.guru_id = 'Wali Kelas wajib dipilih.';
                    } else {
                        this.errors.guru_id = '';
                    }
                }
            },
            submitForm(e) {
                // Jalankan validasi semua field sebelum submit
                this.validate('nama_kelas');
                this.validate('kelas');
                this.validate('guru_id');

                // Jika ada error, batalkan submit
                if (this.errors.nama_kelas || this.errors.kelas || this.errors.guru_id) {
                    e.preventDefault();
                }
                // Jika input masih kosong, batalkan juga dan munculkan error
                if (!this.fields.nama_kelas || !this.fields.kelas || !this.fields.guru_id) {
                    this.validate('nama_kelas');
                    this.validate('kelas');
                    this.validate('guru_id');
                    e.preventDefault();
                }
            }
        }
    }

    // Fungsi Manager Utama (Tabel, Search, Sort, Pagination)
    function manager() {
        return {
            searchQuery: '',
            deleteName: '',
            deleteUrl: '',
            editUrl: '',
            
            // Inisialisasi editData agar tidak error saat x-model membaca propertinya
            editData: {
                nama_kelas: '',
                kelas: '',
                guru_id: ''
            },
            
            // Penampung Error untuk Edit
            editErrors: {
                nama_kelas: '',
                kelas: '',
                guru_id: ''
            },

            items: @json($kelas),
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1,
            itemsPerPage: 5,

            // --- Logika Validasi Edit ---
            validateEdit(field) {
                // Validasi Nama Kelas
                if (field === 'nama_kelas') {
                    if (!this.editData.nama_kelas) {
                        this.editErrors.nama_kelas = 'Nama Kelas wajib diisi.';
                    } else if (this.editData.nama_kelas.length > 100) {
                        this.editErrors.nama_kelas = 'Maksimal 100 karakter.';
                    } else {
                        this.editErrors.nama_kelas = '';
                    }
                }
                
                // Validasi Tingkat Kelas
                if (field === 'kelas') {
                    if (!this.editData.kelas) {
                        this.editErrors.kelas = 'Tingkat/Kelompok wajib diisi.';
                    } else if (this.editData.kelas.length > 1) {
                        this.editErrors.kelas = 'Maksimal 1 karakter (Contoh: A).';
                    } else {
                        this.editErrors.kelas = '';
                    }
                }

                // Validasi Wali Kelas
                if (field === 'guru_id') {
                    if (!this.editData.guru_id) {
                        this.editErrors.guru_id = 'Wali Kelas wajib dipilih.';
                    } else {
                        this.editErrors.guru_id = '';
                    }
                }
            },

            submitEdit(e) {
                // Jalankan validasi semua field sebelum submit
                this.validateEdit('nama_kelas');
                this.validateEdit('kelas');
                this.validateEdit('guru_id');

                // Cek apakah ada error
                if (this.editErrors.nama_kelas || this.editErrors.kelas || this.editErrors.guru_id) {
                    e.preventDefault(); // Batalkan submit jika ada error
                    return;
                }

                // Cek jika field kosong (double check)
                if (!this.editData.nama_kelas || !this.editData.kelas || !this.editData.guru_id) {
                    this.validateEdit('nama_kelas');
                    this.validateEdit('kelas');
                    this.validateEdit('guru_id');
                    e.preventDefault();
                }
            },
            // --- End Logika Validasi Edit ---

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
                        item.nama_kelas.toLowerCase().includes(this.searchQuery.toLowerCase())
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