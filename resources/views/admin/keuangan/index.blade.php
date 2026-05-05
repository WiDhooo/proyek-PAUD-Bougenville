@extends('layouts.app')

@section('title', 'Manajemen Keuangan')

@section('content')
{{-- WRAPPER UTAMA --}}
<div class="container-fluid" x-data="keuanganManager()">
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" 
             style="border-radius: var(--paud-radius-sm);" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- CARD 1: FORM INPUT --}}
    <div class="paud-card mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-pencil-square me-2 text-paud-teal"></i>Input Transaksi SPP
            </h6>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.keuangan.store') }}" method="POST" @submit="submitAdd($event)">
                @csrf
                <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Nama Siswa <span class="text-danger">*</span></label>
                        <select name="siswa_id" class="form-select" x-model="addData.siswa_id" 
                                :class="{'is-invalid': addErrors.siswa_id}" @change="validateAdd('siswa_id')">
                            <option value="" disabled selected>Pilih Nama Siswa</option>
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" x-text="addErrors.siswa_id"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Bulan <span class="text-danger">*</span></label>
                        <select name="bulan" class="form-select" x-model="addData.bulan"
                                :class="{'is-invalid': addErrors.bulan}" @change="validateAdd('bulan')">
                            <option value="" disabled selected>Pilih Bulan</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" x-text="addErrors.bulan"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                        <select name="tahun" class="form-select" x-model="addData.tahun">
                            @for($i = date('Y')-1; $i <= date('Y')+1; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah Pembayaran <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-light border-end-0">Rp</span>
                            <input type="number" name="jumlah" class="form-control border-start-0" placeholder="0" 
                                   x-model="addData.jumlah" :class="{'is-invalid': addErrors.jumlah}" @input="validateAdd('jumlah')">
                            <div class="invalid-feedback" x-text="addErrors.jumlah"></div>
                        </div>
                        <small class="text-muted">Rentang: Rp 20.000 - Rp 1.000.000</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status Pembayaran <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" x-model="addData.status">
                            <option value="Sudah Bayar">Sudah Bayar (Lunas)</option>
                            <option value="Belum Bayar">Belum Bayar (Tunggakan)</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4 d-flex gap-2">
                        <button type="submit" class="btn paud-btn-primary btn-sm px-4">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <button type="reset" class="btn paud-btn-outline btn-sm px-4" @click="resetForm()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
            {{-- Pesan error dari server (seperti proteksi pendaftaran) tetap tampil di sini --}}
            @if ($errors->any())
                <div class="alert alert-danger mt-3 mb-0 py-2 border-0 small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="paud-card">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold mb-0" style="color: var(--paud-text);">
                    <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">
                        <i class="bi bi-clock-history me-2" style="color: var(--paud-teal);"></i>Riwayat Pembayaran
                    </span>
                </h5>
                <div class="input-group" style="max-width: 240px;">
                    <span class="input-group-text bg-paud-teal-light border-0">
                        <i class="bi bi-search text-paud-teal"></i>
                    </span>
                    <input type="search" class="form-control border-0 bg-light" placeholder="Cari nama siswa..." x-model.debounce.300ms="searchQuery">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="paud-thead">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th @click="sortBy('siswa.nama')" style="cursor:pointer; user-select:none;">
                                Nama Siswa <i class="bi bi-arrow-down-up small"></i>
                            </th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Bulan Pembayaran</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr class="paud-table-row">
                                <td class="text-center" x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td class="fw-bold" style="color: #1E293B;" x-text="item.siswa ? item.siswa.nama : '-'"></td>
                                <td class="text-center" x-text="item.kategori"></td>
                                <td class="text-center" x-text="item.bulan_pembayaran"></td>
                                <td class="text-center fw-semibold" x-text="formatRupiah(item.jumlah)"></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2" 
                                          :style="item.status === 'Sudah Bayar' ? 'background-color: #ECFDF5; color: #10B981;' : 'background-color: #FFF7ED; color: #F97316;'"
                                          style="font-weight: 600;">
                                        <i class="bi me-1" :class="item.status === 'Sudah Bayar' ? 'bi-check-circle-fill' : 'bi-clock-history'"></i>
                                        <span x-text="item.status === 'Sudah Bayar' ? 'Lunas' : 'Belum Bayar'"></span>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-primary btn-sm" style="border-radius: 8px;" @click="prepareEdit(item)" data-bs-toggle="modal" data-bs-target="#modalEditKeuangan">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" style="border-radius: 8px;" @click="prepareDelete(item)" data-bs-toggle="modal" data-bs-target="#modalHapusKeuangan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

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

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEditKeuangan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form :action="editUrl" method="POST" @submit="submitEdit($event)">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Siswa: <strong x-text="editName"></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah" class="form-control" x-model="editData.jumlah" 
                                       :class="{'is-invalid': editErrors.jumlah}" @input="validateEdit('jumlah')">
                                <div class="invalid-feedback" x-text="editErrors.jumlah"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select" x-model="editData.status">
                                <option value="Sudah Bayar">Sudah Bayar (Lunas)</option>
                                <option value="Belum Bayar">Belum Bayar (Tunggakan)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-danger btn-sm">Ya, Hapus Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function keuanganManager() {
        return {
            items: @json($data),
            searchQuery: '',
            currentPage: 1,
            itemsPerPage: 5,
            sortColumn: '',
            sortDirection: 'asc',

            // --- STATE TAMBAH ---
            addData: { siswa_id: '', bulan: '', tahun: '{{ date('Y') }}', jumlah: '', status: 'Sudah Bayar' },
            addErrors: { siswa_id: '', bulan: '', jumlah: '' },

            // --- STATE EDIT ---
            editUrl: '',
            editName: '',
            editData: { id: null, jumlah: '', status: '' },
            editErrors: { jumlah: '' },

            // --- STATE HAPUS ---
            deleteUrl: '',
            deleteName: '',

            formatRupiah(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); },

            resetForm() {
                this.addData = { siswa_id: '', bulan: '', tahun: '{{ date('Y') }}', jumlah: '', status: 'Sudah Bayar' };
                this.addErrors = { siswa_id: '', bulan: '', jumlah: '' };
            },

            // --- VALIDASI REAL-TIME (Sama seperti Guru) ---
            validateAdd(field) {
                if(field === 'siswa_id') this.addErrors.siswa_id = this.addData.siswa_id ? '' : 'Nama siswa wajib dipilih.';
                if(field === 'bulan') this.addErrors.bulan = this.addData.bulan ? '' : 'Bulan pembayaran wajib dipilih.';
                if(field === 'jumlah') {
                    if(!this.addData.jumlah) this.addErrors.jumlah = 'Nominal pembayaran wajib diisi.';
                    else if(this.addData.jumlah < 20000) this.addErrors.jumlah = 'Nominal minimal Rp 20.000.';
                    else if(this.addData.jumlah > 1000000) this.addErrors.jumlah = 'Nominal maksimal Rp 1.000.000.';
                    else this.addErrors.jumlah = '';
                }
            },

            submitAdd(e) {
                ['siswa_id', 'bulan', 'jumlah'].forEach(f => this.validateAdd(f));
                const hasError = Object.values(this.addErrors).some(val => val !== '');
                if (hasError) e.preventDefault();
            },

            // --- EDIT & DELETE LOGIC ---
            prepareEdit(item) {
                this.editName = item.siswa ? item.siswa.nama : 'Siswa';
                this.editUrl = `/admin/keuangan/${item.id}`;
                this.editData = { id: item.id, jumlah: item.jumlah, status: item.status };
                this.editErrors = { jumlah: '' };
            },

            validateEdit(field) {
                if(field === 'jumlah') {
                    if(!this.editData.jumlah) this.editErrors.jumlah = 'Nominal wajib diisi.';
                    else if(this.editData.jumlah < 20000) this.editErrors.jumlah = 'Nominal minimal Rp 20.000.';
                    else this.editErrors.jumlah = '';
                }
            },

            submitEdit(e) {
                this.validateEdit('jumlah');
                if (this.editErrors.jumlah !== '') e.preventDefault();
            },

            prepareDelete(item) {
                this.deleteName = item.siswa ? item.siswa.nama : 'Transaksi';
                this.deleteUrl = `/admin/keuangan/${item.id}`;
            },

            // --- SEARCH, SORT, PAGINATION ---
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
                if(this.currentPage > this.totalPages) this.currentPage = 1;
                let start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredItems.slice(start, start + this.itemsPerPage);
            }
        }
    }
</script>
@endsection