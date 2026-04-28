@extends('layouts.app')

@section('title', 'Manajemen Keuangan')

@section('content')
{{-- WRAPPER UTAMA: x-data keuanganManager untuk mengelola state tabel secara reaktif --}}
<div class="container-fluid" x-data="keuanganManager()">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" 
             style="border-radius: var(--paud-radius-sm);" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- CARD 1: FORM INPUT TRANSAKSI (Sesuai Desain Screenshot) --}}
    <div class="paud-card mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-pencil-square me-2 text-paud-teal"></i>Input Transaksi Pemasukan
            </h6>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.keuangan.store') }}" method="POST">
                @csrf
                {{-- Hidden Input untuk default value --}}
                <input type="hidden" name="jenis" value="pemasukan">
                <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                
                <div class="row g-3">
                    {{-- Nama Siswa (Dropdown dari Database) --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Siswa</label>
                        <select name="siswa_id" class="form-select @error('siswa_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Nama Siswa</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        @error('siswa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Bulan Pembayaran --}}
                    <div class="col-md-6">
                        <label class="form-label">Bulan Pembayaran</label>
                        <select name="bulan_pembayaran" class="form-select @error('bulan_pembayaran') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Bulan</option>
                            @php
                                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $year = date('Y');
                            @endphp
                            @foreach($months as $m)
                                <option value="{{ $m }} {{ $year }}">{{ $m }} {{ $year }}</option>
                            @endforeach
                        </select>
                        @error('bulan_pembayaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jumlah Pembayaran --}}
                    <div class="col-md-12">
                        <label class="form-label">Jumlah Pembayaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-color: var(--paud-border);">Rp</span>
                            <input type="number" name="jumlah" class="form-control border-start-0 @error('jumlah') is-invalid @enderror" 
                                   placeholder="0" required style="border-color: var(--paud-border);">
                            @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-12 mt-4 d-flex gap-2">
                        <button type="submit" class="btn paud-btn-primary btn-sm px-4">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <button type="reset" class="btn paud-btn-outline btn-sm px-4">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CARD 2: TABEL RIWAYAT (Sesuai Desain Manajemen Kelas) --}}
    <div class="paud-card">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold mb-0" style="color: var(--paud-text);">
                    <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">
                        <i class="bi bi-clock-history me-2" style="color: var(--paud-teal);"></i>Riwayat Pembayaran dan Laporan Keuangan
                    </span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group" style="max-width: 240px;">
                        <span class="input-group-text bg-paud-teal-light border-0">
                            <i class="bi bi-search text-paud-teal"></i>
                        </span>
                        <input type="search" class="form-control border-0 bg-light" 
                               placeholder="Cari nama siswa..." x-model.debounce.300ms="searchQuery">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="paud-thead">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th @click="sortBy('siswa.nama')" style="cursor:pointer; user-select:none;">
                                Nama Siswa 
                                <i class="bi" :class="sortColumn==='siswa.nama' ? (sortDirection==='asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'"></i>
                            </th>
                            <th>Bulan Pembayaran</th>
                            <th @click="sortBy('jumlah')" style="cursor:pointer; user-select:none;">
                                Jumlah Pembayaran
                                <i class="bi" :class="sortColumn==='jumlah' ? (sortDirection==='asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'"></i>
                            </th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                         <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr class="paud-table-row">
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="fw-semibold" style="color: var(--paud-text);" 
                                    x-text="item.siswa ? item.siswa.nama : 'Data Siswa Hilang'"></td>
                                <td x-text="item.bulan_pembayaran"></td>
                                <td x-text="formatRupiah(item.jumlah)"></td>
                                <td style="text-align:center;">
                                    <span class="paud-badge bg-paud-green-light text-paud-green">Lunas</span>
                                </td>
                                <td style="text-align:center;">
                                    <button type="button" class="btn btn-sm" 
                                            style="border: 1.5px solid var(--paud-coral); color: var(--paud-coral); border-radius: 6px;"
                                            @click="prepareDelete(item)" data-bs-toggle="modal" data-bs-target="#modalHapusKeuangan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        {{-- Empty State --}}
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i> Data tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <nav x-show="totalPages > 1" class="d-flex justify-content-end mt-3">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                        <a class="page-link" href="#" @click.prevent="currentPage--">Sebelumnya</a>
                    </li>
                    <template x-for="page in totalPages" :key="page">
                        <li class="page-item" :class="{ 'active': currentPage === page }">
                            <a class="page-link" href="#" @click.prevent="currentPage = page" x-text="page"></a>
                        </li>
                    </template>
                    <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                        <a class="page-link" href="#" @click.prevent="currentPage++">Berikutnya</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div class="modal fade" id="modalHapusKeuangan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <form :action="deleteUrl" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus riwayat pembayaran milik <strong x-text="deleteName"></strong>?</p>
                        <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-danger btn-sm">Ya, Hapus Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT Alpine.js --}}
<script>
    function keuanganManager() {
        return {
            // Mengambil data dari variabel $data yang dikirim Controller
            items: @json($data), 
            searchQuery: '',
            sortColumn: '',
            sortDirection: 'asc',
            currentPage: 1,
            itemsPerPage: 5,
            
            // State Hapus
            deleteName: '',
            deleteUrl: '',

            formatRupiah(val) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
            },

            prepareDelete(item) {
                this.deleteName = item.siswa ? item.siswa.nama : 'Transaksi';
                this.deleteUrl = `/admin/keuangan/${item.id}`;
            },

            sortBy(col) {
                if(this.sortColumn === col) this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                else { this.sortColumn = col; this.sortDirection = 'asc'; }
            },

            get filteredItems() {
                let filtered = this.items.filter(x => {
                    const nama = x.siswa ? x.siswa.nama.toLowerCase() : '';
                    return nama.includes(this.searchQuery.toLowerCase());
                });

                if(this.sortColumn) {
                    filtered.sort((a,b) => {
                        // Logic untuk akses nested object siswa.nama
                        let va = this.getNestedValue(a, this.sortColumn);
                        let vb = this.getNestedValue(b, this.sortColumn);
                        
                        if (va < vb) return this.sortDirection === 'asc' ? -1 : 1;
                        if (va > vb) return this.sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    });
                }
                return filtered;
            },

            getNestedValue(obj, path) {
                return path.split('.').reduce((o, i) => (o ? o[i] : ''), obj);
            },

            get totalPages() { return Math.ceil(this.filteredItems.length / this.itemsPerPage); },
            
            get paginatedItems() {
                // Reset ke page 1 jika hasil filter berubah
                if(this.totalPages > 0 && this.currentPage > this.totalPages) this.currentPage = 1;
                let start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredItems.slice(start, start + this.itemsPerPage);
            }
        }
    }
</script>
@endsection