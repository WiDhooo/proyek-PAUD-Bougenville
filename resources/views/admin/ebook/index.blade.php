@extends('layouts.app')

@section('title', 'Manajemen E-Book')

@section('content')
<div class="container-fluid" x-data="ebookManager()">
    
    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" 
             style="border-radius: var(--paud-radius-sm);" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- MAIN CARD --}}
    <div class="paud-card">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="fw-bold mb-0" style="color: var(--paud-text);">
                    <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">
                        <i class="bi bi-journal-bookmark-fill me-2" style="color: var(--paud-teal);"></i>Data E-Book Interaktif
                    </span>
                </h5>
                
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group" style="max-width: 240px;">
                        <span class="input-group-text bg-paud-teal-light border-0">
                            <i class="bi bi-search text-paud-teal"></i>
                        </span>
                        <input type="search" class="form-control border-0 bg-light" 
                               placeholder="Cari judul..." x-model.debounce.300ms="searchQuery">
                    </div>
                    <button type="button" class="btn paud-btn-primary btn-sm flex-shrink-0"
                            data-bs-toggle="modal" data-bs-target="#modalTambahEbook">
                        <i class="bi bi-plus-lg me-1"></i> Tambah E-Book
                    </button>
                </div>
            </div>

            {{-- Entries Selector --}}
            <div class="d-flex align-items-center gap-2 mb-3" style="font-size: 0.85rem; color: var(--paud-muted);">
                <span>Tampilkan</span>
                <select class="form-select form-select-sm w-auto border-0 bg-light shadow-none" x-model="itemsPerPage" style="border-radius: 6px;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                </select>
                <span>entri</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="paud-thead">
                        <tr>
                            <th style="width: 80px;">Sampul</th>
                            <th @click="sortBy('judul')" style="cursor:pointer; user-select:none;">
                                Judul Buku
                                <i class="bi" :class="sortColumn==='judul' ? (sortDirection==='asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'"></i>
                            </th>
                            <th>Halaman</th>
                            <th>Ukuran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in paginatedItems" :key="item.id">
                            <tr class="paud-table-row">
                                <td>
                                    <div class="rounded shadow-sm bg-light" style="width: 50px; aspect-ratio: 3/4; overflow: hidden; border: 1px solid var(--paud-border);">
                                        <template x-if="item.thumbnail">
                                            <img :src="'/storage/' + item.thumbnail" style="width:100%;height:100%;object-fit:cover;">
                                        </template>
                                        <template x-if="!item.thumbnail && item.file_path && item.file_path.length > 0">
                                            <img :src="'/storage/' + item.file_path[0].image" style="width:100%;height:100%;object-fit:cover;opacity:0.5;">
                                        </template>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold" style="color: var(--paud-text);" x-text="item.judul"></div>
                                    <div class="text-muted small text-truncate" style="max-width: 250px;" x-text="item.deskripsi || '-'"></div>
                                </td>
                                <td>
                                    <span class="paud-badge bg-paud-teal-light text-paud-teal" x-text="item.file_path.length + ' Hal'"></span>
                                </td>
                                <td>
                                    <span class="text-muted small" x-text="(item.ukuran_file / 1024 / 1024).toFixed(2) + ' MB'"></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-warning" 
                                                @click="openEditModal(item)"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditEbook">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm" @click="confirmDelete(item.id)"
                                                style="border: 1.5px solid var(--paud-coral); color: var(--paud-coral); border-radius: 6px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan <span class="fw-bold text-dark" x-text="filteredItems.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0"></span>
                    sampai <span class="fw-bold text-dark" x-text="Math.min(currentPage * itemsPerPage, filteredItems.length)"></span>
                    dari <span class="fw-bold text-dark" x-text="filteredItems.length"></span> entri
                </div>
                <nav x-show="totalPages > 1">
                    <ul class="pagination pagination-sm mb-0" style="--bs-pagination-active-bg: var(--paud-teal); --bs-pagination-active-border-color: var(--paud-teal);">
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
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambahEbook" tabindex="-1" x-data="ebookHandler()">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form action="{{ route('admin.ebook.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="fw-bold" style="color: var(--paud-teal);"><i class="bi bi-plus me-2"></i>Tambah E-Book Interaktif</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            {{-- SISI KIRI: INFO DASAR --}}
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Judul Buku</label>
                                    <input type="text" name="judul" class="form-control bg-light border-0" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control bg-light border-0" rows="4"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Sampul (Optional)</label>
                                    <input type="file" name="thumbnail" class="form-control form-control-sm">
                                </div>
                                
                                <div class="p-3 bg-paud-teal-light rounded-3 border border-info">
                                    <label class="fw-bold small d-block mb-2 text-dark">Langkah 1: Pilih Materi</label>
                                    {{-- Input gambar utama yang mentrigger daftar baris di kanan --}}
                                    <input type="file" name="file_gambar[]" class="form-control form-control-sm" 
                                           multiple accept="image/*" required @change="handleFiles($event)">
                                    <small class="text-muted mt-1 d-block xsmall">Pilih banyak gambar sekaligus.</small>
                                </div>
                            </div>

                            {{-- SISI KANAN: DAFTAR BARIS HALAMAN YANG DIGENERATE OTOMATIS --}}
                            <div class="col-md-9 border-start">
                                <label class="form-label fw-bold small mb-3">
                                    Langkah 2: Kelola Suara Halaman
                                </label>

                                <div class="overflow-auto px-2" style="max-height: 500px;">
                                    {{-- Pesan jika gambar belum dipilih --}}
                                    <template x-if="pagePreviews.length === 0">
                                        <div class="text-center py-5 border rounded bg-light" style="border-style: dashed !important;">
                                            <i class="bi bi-images fs-2 text-muted"></i>
                                            <p class="text-muted small mt-2">Silakan pilih gambar materi terlebih dahulu di sisi kiri.</p>
                                        </div>
                                    </template>

                                    {{-- Baris Halaman yang muncul otomatis setelah gambar dipilih --}}
                                    <div class="row g-3">
                                        <template x-for="(page, index) in pagePreviews" :key="index">
                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm bg-white overflow-hidden">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex align-items-center gap-3">
                                                            {{-- Preview Gambar Materi --}}
                                                            <div style="width: 80px; aspect-ratio: 3/4; flex-shrink: 0;">
                                                                <img :src="page.url" class="w-100 h-100 object-cover rounded border">
                                                            </div>

                                                            {{-- Info Nomor Halaman --}}
                                                            <div style="width: 70px;">
                                                                <label class="xsmall fw-bold text-muted d-block text-center mb-1">Halaman</label>
                                                                <div class="form-control form-control-sm text-center fw-bold bg-light" x-text="index + 1"></div>
                                                                {{-- Input hidden untuk mapping audio_page ke controller --}}
                                                                <input type="hidden" :name="'audio_page['+index+']'" :value="index + 1">
                                                            </div>

                                                            {{-- Input Suara untuk Halaman Ini --}}
                                                            <div class="flex-grow-1">
                                                                <label class="xsmall fw-bold text-primary mb-1 d-block">
                                                                    <i class="bi bi-volume-up-fill"></i> Tambah Suara (Optional)
                                                                </label>
                                                                {{-- Gunakan index yang sama untuk mempermudah Controller --}}
                                                                <input type="file" :name="'audio_file['+index+']'" 
                                                                       class="form-control form-control-sm border-0 bg-light" accept="audio/*">
                                                            </div>

                                                            {{-- Tombol hapus baris ini saja --}}
                                                            <button type="button" @click="removeSpecificPage(index)" class="btn btn-sm text-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn paud-btn-primary w-100 py-3 fw-bold rounded-3">Simpan E-Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEditEbook" tabindex="-1" x-data="audioHandler()">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form :action="'/admin/ebook/' + editData.id + '/update'" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0 pt-4 px-4">
                        <h5 class="fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Detail & Edit Materi E-Book</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row g-4">
                            {{-- SISI KIRI: INFO DASAR --}}
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Judul Buku</label>
                                    <input type="text" name="judul" class="form-control bg-light border-0" x-model="editData.judul" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control bg-light border-0" rows="4" x-model="editData.deskripsi"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Ganti Sampul Utama</label>
                                    <input type="file" name="thumbnail" class="form-control form-control-sm">
                                </div>
                                <div class="p-3 bg-blue-50 rounded-3 border border-blue-100">
                                    <label class="fw-bold small d-block mb-2 text-primary">Tambah Halaman Baru</label>
                                    <input type="file" name="add_new_pages[]" class="form-control form-control-sm" multiple accept="image/*">
                                    <small class="text-muted mt-1 d-block xsmall">*Muncul di paling akhir.</small>
                                </div>
                            </div>

                            {{-- SISI KANAN: DAFTAR HALAMAN INTERAKTIF --}}
                            <div class="col-md-9 border-start">
                                <label class="form-label fw-bold small mb-3 d-flex justify-content-between">
                                    Daftar Halaman & Suara
                                    <span class="badge bg-light text-dark border" x-text="editData.file_path ? editData.file_path.length + ' Total' : ''"></span>
                                </label>

                                <div class="overflow-auto px-2" style="max-height: 500px;">
                                    <div class="row g-3">
                                        <template x-for="(page, index) in editData.file_path" :key="index">
                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm bg-white overflow-hidden">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex align-items-center gap-3">
                                                            {{-- Preview Gambar --}}
                                                            <div class="position-relative" style="width: 80px; aspect-ratio: 3/4; flex-shrink: 0;">
                                                                <img :src="'/storage/' + page.image" class="w-100 h-100 object-cover rounded shadow-sm border">
                                                                <button type="button" @click="removePage(index)" class="btn btn-danger btn-xs position-absolute top-0 start-0 m-1 rounded-circle" title="Hapus Halaman">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            </div>

                                                            {{-- Kontrol Posisi --}}
                                                            <div style="width: 70px;">
                                                                <label class="xsmall fw-bold text-muted d-block text-center mb-1">Posisi</label>
                                                                <input type="number" :name="'existing_pages['+index+'][order]'" 
                                                                    class="form-control form-control-sm text-center fw-bold" 
                                                                    :value="index + 1" min="1" :max="editData.file_path.length">
                                                            </div>

                                                            {{-- Kontrol Suara --}}
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="xsmall fw-bold text-primary"><i class="bi bi-volume-up-fill"></i> Suara Halaman</span>
                                                                    <template x-if="page.audio">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <span class="badge bg-success-light text-success xsmall">Tersimpan</span>
                                                                            <button type="button" @click="page.audio = null" class="btn btn-link text-danger p-0 xsmall text-decoration-none">Hapus Suara</button>
                                                                        </div>
                                                                    </template>
                                                                </div>

                                                                <input type="file" :name="'update_audio['+index+']'" class="form-control form-control-sm border-0 bg-light" accept="audio/*">
                                                                
                                                                {{-- Hidden Data --}}
                                                                <input type="hidden" :name="'existing_pages['+index+'][image]'" :value="page.image">
                                                                <input type="hidden" :name="'existing_pages['+index+'][old_audio]'" :value="page.audio">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white fw-bold px-5">Simpan Perubahan Materi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .btn-xs { padding: 1px 8px; font-size: 0.75rem; }
    .xsmall { font-size: 0.7rem; }
    .italic { font-style: italic; }
    .paud-badge { font-size: 0.78rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .bg-paud-teal-light { background-color: var(--paud-teal-light) !important; }
    .text-paud-teal { color: var(--paud-teal) !important; }
</style>

<script>
    function ebookManager() {
        return {
            items: @json($data),
            searchQuery: '', sortColumn: 'judul', sortDirection: 'asc', currentPage: 1, itemsPerPage: 10,
            editData: {},
            
            openEditModal(item) {
                // Gunakan Deep Copy agar perubahan di modal tidak langsung merusak tabel di belakang
                this.editData = JSON.parse(JSON.stringify(item));
                
                if (typeof this.editData.file_path === 'string') {
                    this.editData.file_path = JSON.parse(this.editData.file_path);
                }
                const modal = new bootstrap.Modal(document.getElementById('modalEditEbook'));
                modal.show();
            },

            removePage(index) {
                if(confirm('Hapus halaman ini secara permanen dari buku?')) {
                    this.editData.file_path.splice(index, 1);
                }
            },

            addEditAudio() { this.newEditAudios.push({}); },
            removeEditAudio(idx) { this.newEditAudios.splice(idx, 1); },

            sortBy(col) {
                if (this.sortColumn === col) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = col;
                    this.sortDirection = 'asc';
                }
            },

            get filteredItems() {
                let filtered = this.items.filter(i =>
                    i.judul.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
                if (this.sortColumn) {
                    filtered.sort((a, b) => {
                        let va = a[this.sortColumn] || '', vb = b[this.sortColumn] || '';
                        return this.sortDirection === 'asc' ? va.localeCompare(vb) : vb.localeCompare(va);
                    });
                }
                return filtered;
            },

            get totalPages() { return Math.ceil(this.filteredItems.length / this.itemsPerPage); },

            get paginatedItems() {
                if (this.totalPages > 0 && this.currentPage > this.totalPages) {
                    this.currentPage = 1;
                }
                let start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredItems.slice(start, start + this.itemsPerPage);
            },

            confirmDelete(id) {
                if (confirm('Hapus E-Book ini? File fisik juga akan dihapus.')) {
                    const form = document.getElementById('delete-form');
                    form.action = '/admin/ebook/' + id;
                    form.submit();
                }
            }
        }
    }

    function ebookHandler() {
        return {
            pagePreviews: [],
            
            // Fungsi ketika input gambar berubah
            handleFiles(event) {
                const files = event.target.files;
                this.pagePreviews = []; // Reset list

                // Loop file yang dipilih untuk dibuatkan preview URL
                Array.from(files).forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.pagePreviews.push({
                            url: e.target.result,
                            name: file.name
                        });
                    };
                    reader.readAsDataURL(file);
                });
            },

            // Fungsi untuk menghapus salah satu halaman yang sudah masuk list preview
            removeSpecificPage(index) {
                this.pagePreviews.splice(index, 1);
                // Catatan: Menghapus di preview tidak menghapus file di input "file_gambar[]" 
                // karena keterbatasan browser, namun admin bisa memilih ulang file jika ada salah.
            }
        }
    }
</script>
@endsection