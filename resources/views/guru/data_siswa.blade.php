@extends('layouts.guru')

@section('title', 'Data Siswa')

@section('content')
{{-- x-data="manager()" menginisialisasi state Alpine.js --}}
<div class="container-fluid" x-data="manager()">
    <div class="card border-0 shadow-sm">
        {{-- Header Card: Judul & Pencarian --}}
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Siswa di Kelas Anda</h5>
            <div class="input-group" style="width: 300px;">
                 <input type="search" class="form-control" placeholder="Cari Nama Siswa..." x-model.debounce.300ms="searchQuery">
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            {{-- Header dengan Sort --}}
                            <th @click="sortBy('nama')" style="cursor: pointer;">
                                Nama Siswa
                                <span x-show="sortColumn === 'nama'">
                                    <i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i>
                                </span>
                            </th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop Data Siswa menggunakan Alpine x-for --}}
                        <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr>
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td x-text="item.nis"></td>
                                <td x-text="item.nama"></td>
                                <td x-text="item.jenis_kelamin"></td>
                                <td>
                                    {{-- Tombol Detail: Mengisi variable 'detail' di Alpine saat diklik --}}
                                    <button 
                                        class="btn btn-info btn-sm text-white"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailModal"
                                        @click="detail = item"> 
                                        <i class="bi bi-info-circle-fill"></i> Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        </template>

                        {{-- Pesan jika data tidak ditemukan --}}
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="5" class="text-center text-muted py-4">
                                Data siswa tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination Controls --}}
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

    {{-- Modal Detail Siswa (Reaktif menggunakan Alpine x-text) --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalLabel">Detail Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Data akan otomatis berubah sesuai tombol yang diklik --}}
                    <table class="table table-borderless">
                        <tr>
                            <th width="120">NIS</th>
                            <td>: <span x-text="detail.nis"></span></td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>: <span x-text="detail.nama"></span></td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>: <span x-text="detail.jenis_kelamin"></span></td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            {{-- Menampilkan '-' jika tanggal_lahir kosong --}}
                            <td>: <span x-text="detail.tanggal_lahir ? detail.tanggal_lahir : '-'"></span></td>
                        </tr>
                        <!-- <tr>
                            <th>Alamat</th>
                            <td>: <span x-text="detail.alamat ? detail.alamat : '-'"></span></td>
                        </tr> -->
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script Logic Alpine.js --}}
<script>
    function manager() {
        return {
            searchQuery: '',
            sortColumn: 'nama',
            sortDirection: 'asc',
            currentPage: 1,
            itemsPerPage: 10,
            
            // Variable untuk menampung data detail modal
            detail: {}, 

            // Mengambil data dari Controller Laravel dan mengubahnya jadi JSON
            items: @json($siswa), 

            // Fungsi Sorting
            sortBy(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            },

            // Fungsi Filter & Search
            get filteredItems() {
                let filtered = [...this.items];
                
                // Logic Search
                if (this.searchQuery) {
                    const lowerQuery = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(item =>
                        (item.nama && item.nama.toLowerCase().includes(lowerQuery)) ||
                        (item.nis && item.nis.toString().includes(lowerQuery))
                    );
                }

                // Logic Sort
                if (this.sortColumn) {
                    filtered.sort((a, b) => {
                        let valA = a[this.sortColumn] ? a[this.sortColumn].toString().toLowerCase() : '';
                        let valB = b[this.sortColumn] ? b[this.sortColumn].toString().toLowerCase() : '';
                        
                        if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                        if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    });
                }
                return filtered;
            },
            
            // Logic Pagination
            get totalPages() {
                return Math.ceil(this.filteredItems.length / this.itemsPerPage) || 1;
            },
            get paginatedItems() {
                // Reset ke halaman 1 jika hasil filter lebih sedikit dari halaman saat ini
                if (this.currentPage > this.totalPages) {
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