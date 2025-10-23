@extends('layouts.guru')

@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid" x-data="manager()">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Siswa di Kelas Anda</h5>
            <div class="input-group" style="width: 300px;">
                 <input type="search" class="form-control" placeholder="Cari Nama Siswa..." x-model.debounce.300ms="searchQuery">
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
                                <td x-text="item.jenis_kelamin"></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-info-circle-fill"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="5" class="text-center text-muted">
                                Data siswa tidak ditemukan.
                            </td>
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

<script>
    function manager() {
        return {
            searchQuery: '',
            items: @json($murid),
            
            // Variabel untuk sorting & pagination
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1,
            itemsPerPage: 5, // Ubah angka ini sesuai kebutuhan

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