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
                <table class="table table-hover align-middle">
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
                                    <button 
                                        class="btn btn-info btn-sm text-white btn-detail"
                                        data-nis="{{ $item['nis'] }}"
                                        data-nama="{{ $item['nama'] }}"
                                        data-jenis="{{ $item['jenis_kelamin'] }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailModal">
                                        <i class="bi bi-info-circle-fill"></i> Lihat Detail
                                    </button>
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

{{-- Modal Detail Siswa --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Detail Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p><strong>NIS:</strong> <span id="detailNis"></span></p>
                <p><strong>Nama:</strong> <span id="detailNama"></span></p>
                <p><strong>Jenis Kelamin:</strong> <span id="detailJenis"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Bagian Modal Detail ---
    const detailButtons = document.querySelectorAll('.btn-detail');
    const nisField = document.getElementById('detailNis');
    const namaField = document.getElementById('detailNama');
    const jenisField = document.getElementById('detailJenis');

    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            nisField.textContent = this.dataset.nis;
            namaField.textContent = this.dataset.nama;
            jenisField.textContent = this.dataset.jenis;
        });
    });

    // --- Bagian Search / Filter ---
    const searchInput = document.querySelector('input[type="search"]');
    const rows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('keyup', function() {
        const keyword = this.value.toLowerCase().trim();
        rows.forEach(row => {
            const columns = row.querySelectorAll('td');
            let match = false;

            // Cek setiap kolom (NIS, Nama, Jenis Kelamin)
            columns.forEach(col => {
                if (col.textContent.toLowerCase().includes(keyword)) {
                    match = true;
                }
            });

            row.style.display = match ? '' : 'none';
        });
    });
});
</script>
@endpush
