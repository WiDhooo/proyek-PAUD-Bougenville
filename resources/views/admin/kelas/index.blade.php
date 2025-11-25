@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
{{-- WRAPPER UTAMA: x-data harus membungkus SEMUA modal --}}
<div class="container-fluid" x-data="manager()">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                         <template x-for="item in paginatedItems" :key="item.id">
                            <tr>
                                <td x-text="item.nama_kelas"></td>
                                <td x-text="item.kelas"></td>
                                <td x-text="item.wali"></td>
                                <td>
                                    <a :href="`/admin/kelas/${item.id}`" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-info-circle-fill"></i> Detail
                                    </a>
                                    
                                    {{-- TOMBOL EDIT --}}
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalEditKelas"
                                        @click="prepareEdit(item)">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    
                                    {{-- TOMBOL HAPUS --}}
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusKelas"
                                        @click="prepareDelete(item)">
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

            {{-- PAGINATION --}}
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
    {{-- MODAL TAMBAH KELAS --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalTambahKelas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.kelas.store') }}" method="POST" @submit="submitAdd($event)">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Kelas Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Validasi Nama Kelas --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="nama_kelas" 
                                   placeholder="Contoh: Mandiri, Kreatif" 
                                   x-model="addData.nama_kelas" 
                                   @input="validateAdd('nama_kelas')"
                                   :class="{'is-invalid': addErrors.nama_kelas}">
                            <div class="invalid-feedback" x-text="addErrors.nama_kelas"></div>
                        </div>

                        {{-- Validasi Tingkat --}}
                        <div class="mb-3">
                            <label class="form-label">Tingkat/Kelompok</label>
                            <input type="text" class="form-control" name="kelas" 
                                   placeholder="Contoh: A (Maks 1 Karakter)" 
                                   x-model="addData.kelas" 
                                   @input="validateAdd('kelas')"
                                   :class="{'is-invalid': addErrors.kelas}">
                            <div class="invalid-feedback" x-text="addErrors.kelas"></div>
                        </div>

                        {{-- Validasi Wali Kelas --}}
                        <div class="mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <select class="form-select" name="guru_id" 
                                    x-model="addData.guru_id" 
                                    @change="validateAdd('guru_id')"
                                    :class="{'is-invalid': addErrors.guru_id}">
                                <option value="" disabled selected>Pilih seorang guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="addErrors.guru_id"></div>
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

    {{-- ================================================================================= --}}
    {{-- MODAL EDIT KELAS --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalEditKelas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">     
                <form :action="editUrl" method="POST" @submit="submitEdit($event)">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="nama_kelas" 
                                x-model="editData.nama_kelas"
                                @input="validateEdit('nama_kelas')"
                                :class="{'is-invalid': editErrors.nama_kelas}">
                            <div class="invalid-feedback" x-text="editErrors.nama_kelas"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tingkat/Kelompok</label>
                            <input type="text" class="form-control" name="kelas" 
                                x-model="editData.kelas"
                                @input="validateEdit('kelas')"
                                :class="{'is-invalid': editErrors.kelas}">
                            <div class="invalid-feedback" x-text="editErrors.kelas"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <select class="form-select" name="guru_id" 
                                    x-model="editData.guru_id"
                                    @change="validateEdit('guru_id')"
                                    :class="{'is-invalid': editErrors.guru_id}">
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
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

    {{-- ================================================================================= --}}
    {{-- MODAL HAPUS KELAS (DIPERBAIKI) --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalHapusKelas" tabindex="-1">
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
                        <p>Anda yakin ingin menghapus data kelas: 
                        <strong x-text="deleteName"></strong>?
                        </p>
                        <p class="text-danger mb-0 small">Data siswa di kelas ini akan menjadi 'Tanpa Kelas'.</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div> {{-- Penutup Container Utama x-data --}}

<script>
    function manager() {
        return {
            items: @json($kelas),
            searchQuery: '',
            
            // Sort & Pagination
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1,
            itemsPerPage: 5,

            // STATE TAMBAH
            addData: { nama_kelas: '', kelas: '', guru_id: '' },
            addErrors: { nama_kelas: '', kelas: '', guru_id: '' },

            // STATE EDIT
            editUrl: '',
            editData: { nama_kelas: '', kelas: '', guru_id: '' },
            editErrors: { nama_kelas: '', kelas: '', guru_id: '' },

            // STATE DELETE
            deleteName: '',
            deleteUrl: '',

            // --- VALIDASI TAMBAH ---
            validateAdd(field) {
                const d = this.addData; const e = this.addErrors;
                if(field === 'nama_kelas') e.nama_kelas = d.nama_kelas ? (d.nama_kelas.length > 100 ? 'Maks 100 karakter' : '') : 'Wajib diisi';
                if(field === 'kelas') e.kelas = d.kelas ? (d.kelas.length > 1 ? 'Maks 1 karakter' : '') : 'Wajib diisi';
                if(field === 'guru_id') e.guru_id = d.guru_id ? '' : 'Wajib dipilih';
            },
            submitAdd(ev) {
                ['nama_kelas', 'kelas', 'guru_id'].forEach(f => this.validateAdd(f));
                if(Object.values(this.addErrors).some(x => x !== '')) ev.preventDefault();
            },

            // --- VALIDASI EDIT ---
            prepareEdit(item) {
                this.editData = {
                    nama_kelas: item.nama_kelas,
                    kelas: item.kelas,
                    guru_id: item.guru_id
                };
                this.editUrl = `/admin/kelas/${item.id}`;
                this.editErrors = { nama_kelas: '', kelas: '', guru_id: '' };
            },
            validateEdit(field) {
                const d = this.editData; const e = this.editErrors;
                if(field === 'nama_kelas') e.nama_kelas = d.nama_kelas ? (d.nama_kelas.length > 100 ? 'Maks 100 karakter' : '') : 'Wajib diisi';
                if(field === 'kelas') e.kelas = d.kelas ? (d.kelas.length > 1 ? 'Maks 1 karakter' : '') : 'Wajib diisi';
                if(field === 'guru_id') e.guru_id = d.guru_id ? '' : 'Wajib dipilih';
            },
            submitEdit(ev) {
                ['nama_kelas', 'kelas', 'guru_id'].forEach(f => this.validateEdit(f));
                if(Object.values(this.editErrors).some(x => x !== '')) ev.preventDefault();
            },

            // --- LOGIC HAPUS (Fix Error Method Not Allowed) ---
            prepareDelete(item) {
                this.deleteName = item.nama_kelas;
                // Pastikan item.id ada. URL harus diakhiri ID, misal: /admin/kelas/5
                this.deleteUrl = `/admin/kelas/${item.id}`;
            },

            // --- UTILITIES ---
            sortBy(col) {
                if(this.sortColumn === col) this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                else { this.sortColumn = col; this.sortDirection = 'asc'; }
            },
            get filteredItems() {
                let items = [...this.items];
                if(this.searchQuery) items = items.filter(x => x.nama_kelas.toLowerCase().includes(this.searchQuery.toLowerCase()));
                if(this.sortColumn) {
                    items.sort((a,b) => {
                        let va = a[this.sortColumn]||'', vb = b[this.sortColumn]||'';
                        return this.sortDirection === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
                    });
                }
                return items;
            },
            get totalPages() { return Math.ceil(this.filteredItems.length / this.itemsPerPage); },
            get paginatedItems() {
                if(this.totalPages > 0 && this.currentPage > this.totalPages) this.currentPage = 1;
                let start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredItems.slice(start, start + this.itemsPerPage);
            }
        }
    }
</script>
@endsection