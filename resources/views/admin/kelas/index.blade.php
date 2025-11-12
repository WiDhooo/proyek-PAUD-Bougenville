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

            <div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-labelledby="modalTambahKelasLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        
                        {{-- Form ini akan dikirim ke route 'admin.kelas.store' --}}
                        <form action="{{ route('admin.kelas.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTambahKelasLabel">Buat Kelas Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="add-nama_kelas" class="form-label">Nama Kelas</label>
                                    <input type="text" class="form-control" id="add-nama_kelas" name="nama_kelas" placeholder="Contoh: Mandiri, Kreatif, Ceria" required>
                                </div>
                                <div class="mb-3">
                                    <label for="add-kelas" class="form-label">Tingkat/Kelompok</label>
                                    <input type="text" class="form-control" id="add-kelas" name="kelas" placeholder="Contoh: A, B, Kelompok Bermain" required>
                                </div>
                                <div class="mb-3">
                                    <label for="add-wali_id" class="form-label">Wali Kelas</label>
                                    <select class="form-select" id="add-wali_id" name="guru_id" required>
                                        <option value="" disabled selected>Pilih seorang guru</option>
                                        
                                        @isset($gurus)
                                            @foreach($gurus as $guru)
                                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                            @endforeach
                                        @else
                                            <option disabled>Data guru tidak ditemukan</option>
                                        @endisset
                                    </select>
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

            <div class="modal fade" id="modalEditKelas" tabindex="-1" aria-labelledby="modalEditKelasLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        
                        <form :action="editUrl" method="POST">
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
                                        x-model="editData.nama_kelas" required>
                                </div>
                                
                                {{-- Input 2: Tingkat/Kelompok (A/B) --}}
                                <div class="mb-3">
                                    <label for="upd-kelas" class="form-label">Tingkat/Kelompok</label>
                                    <input type="text" class="form-control" id="upd-kelas" name="kelas" 
                                        x-model="editData.kelas" required>
                                </div>
                                
                                {{-- Input 3: Wali Kelas (Dropdown) --}}
                                <div class="mb-3">
                                    <label for="upd-wali_id" class="form-label">Wali Kelas</label>
                                    <select class="form-select" id="upd-wali_id" name="guru_id" 
                                            x-model="editData.guru_id" required>
                                        
                                        {{-- Catatan: editData.guru_id akan otomatis memilih guru yang benar --}}
                                        
                                        @isset($gurus)
                                            @foreach($gurus as $guru)
                                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                            @endforeach
                                        @else
                                            <option disabled>Data guru tidak ditemukan</option>
                                        @endisset
                                    </select>
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
            items: @json($kelas),
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1,
            itemsPerPage: 5,

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