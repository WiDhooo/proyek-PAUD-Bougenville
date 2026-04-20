@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
{{-- WRAPPER UTAMA: x-data harus membungkus SEMUA modal --}}
<div class="container-fluid" x-data="manager()">
    
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
                        <i class="bi bi-grid-3x3-gap-fill me-2" style="color: var(--paud-teal);"></i>Data Kelas
                    </span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group" style="max-width: 240px;">
                        <span class="input-group-text" style="border-color: var(--paud-border); background: var(--paud-teal-light); border-radius: var(--paud-radius-sm) 0 0 var(--paud-radius-sm);">
                            <i class="bi bi-search" style="color: var(--paud-muted);"></i>
                        </span>
                        <input type="search" class="form-control" placeholder="Cari nama kelas..." x-model.debounce.300ms="searchQuery"
                               style="border-radius: 0 var(--paud-radius-sm) var(--paud-radius-sm) 0;">
                    </div>
                    <button type="button" class="btn paud-btn-primary btn-sm flex-shrink-0"
                            data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                        <i class="bi bi-plus-lg me-1"></i> Buat Kelas
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="paud-thead">
                        <tr>
                            <th @click="sortBy('nama_kelas')" style="cursor:pointer; user-select:none;">
                                Nama Kelas
                                <i class="bi" :class="sortColumn==='nama_kelas' ? (sortDirection==='asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'"></i>
                            </th>
                            <th>Tingkat</th>
                            <th @click="sortBy('wali')" style="cursor:pointer; user-select:none;">
                                Wali Kelas
                                <i class="bi" :class="sortColumn==='wali' ? (sortDirection==='asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'"></i>
                            </th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                         <template x-for="item in paginatedItems" :key="item.id">
                            <tr class="paud-table-row">
                                <td class="fw-semibold" style="color: var(--paud-text);" x-text="item.nama_kelas"></td>
                                <td>
                                    <span class="paud-badge bg-paud-teal-light text-paud-teal" x-text="`Kelompok ${item.kelas}`"></span>
                                </td>
                                <td style="color: var(--paud-muted); font-size:0.88rem;" x-text="item.wali"></td>
                                <td style="text-align:center;">
                                    <a :href="`/admin/kelas/${item.id}`"
                                        class="btn btn-sm me-1"
                                        style="border: 1.5px solid var(--paud-teal); color: var(--paud-teal); border-radius: 6px;"
                                        title="Detail">
                                        <i class="bi bi-info-circle"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm me-1"
                                        style="border: 1.5px solid var(--paud-amber); color: var(--paud-amber); border-radius: 6px;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditKelas"
                                        @click="prepareEdit(item)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm"
                                        style="border: 1.5px solid var(--paud-coral); color: var(--paud-coral); border-radius: 6px;"
                                        data-bs-toggle="modal" data-bs-target="#modalHapusKelas"
                                        @click="prepareDelete(item)" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                         <tr x-show="filteredItems.length === 0">
                            <td colspan="4" class="text-center py-4" style="color: var(--paud-muted);">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i> Data tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
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
    {{-- MODAL TAMBAH KELAS --}}
    {{-- ================================================================================= --}}
    <div class="modal fade" id="modalTambahKelas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.kelas.store') }}" method="POST" @submit="submitAdd($event)">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2" style="color:var(--paud-teal);"></i>Buat Kelas Baru</h5>
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
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Simpan</button>
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
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2" style="color:var(--paud-amber);"></i>Edit Data Kelas</h5>
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
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Simpan Perubahan</button>
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
                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2" style="color:var(--paud-coral);"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Anda yakin ingin menghapus data kelas: 
                        <strong x-text="deleteName"></strong>?
                        </p>
                        <p class="text-danger mb-0 small">Data siswa di kelas ini akan menjadi 'Tanpa Kelas'.</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-danger btn-sm"><i class="bi bi-trash me-1"></i> Ya, Hapus</button>
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