@extends('layouts.app')

@section('title', 'Kelola Galeri')

@section('content')
<div class="container-fluid" x-data="{ deleteUrl: '', deleteTitle: '' }">
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('admin.profil.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h3 class="fw-bold d-inline-block ms-3 mb-0">Kelola Galeri</h3>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahFoto">
            <i class="bi bi-plus-lg"></i> Tambah Kegiatan Baru
        </button>
    </div>

    {{-- LOGIKA UTAMA: Cek apakah ada data --}}
    @if ($galeris->count() > 0)
        
        {{-- JIKA ADA DATA: Tampilkan Grid Row --}}
        <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">
            @foreach ($galeris as $item)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('images/galeri/' . $item['gambar']) }}" class="card-img-top w-100" style="height: 200px; object-fit: cover;" alt="{{ $item['judul'] }}">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-truncate" title="{{ $item->judul }}">
                                {{ $item->judul }}
                            </h6>
                            <p class="card-text small text-muted">
                                {{ Str::limit($item->deskripsi, 80) }}
                            </p>
                        </div>
                        <div class="card-footer text-end bg-white border-top-0">
                            <button class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalHapusFoto" 
                                @click="deleteUrl = '{{ route('admin.galeri.destroy', $item->id) }}'; deleteTitle = '{{ $item->judul }}'">
                                    Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else

        {{-- JIKA KOSONG: Tampilkan Wadah Full Width (Tanpa Row Grid) --}}
        <div class="d-flex flex-column justify-content-center align-items-center text-center rounded-3 border-2 border-dashed border-light py-5" style="min-height: 60vh; background-color: #f9f9f9;">
            <div class="mb-3">
                <i class="bi bi-images display-1 text-secondary opacity-25"></i>
            </div>
            <h5 class="fw-bold text-secondary">Galeri Masih Kosong</h5>
            <p class="text-muted small mb-0">
                Belum ada foto kegiatan yang diunggah.
            </p>
        </div>

    @endif

    {{-- Modal Tambah Foto --}}
    <div class="modal fade" id="modalTambahFoto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Foto Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Kegiatan</label>
                            <input type="text" class="form-control" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Singkat Kegiatan</label>
                            <input type="text" class="form-control" name="deskripsi" required>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Pilih File Foto</label>
                            <input class="form-control" type="file" name="gambar" accept="image/*" required>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Hapus Foto --}}
    <div class="modal fade" id="modalHapusFoto" tabindex="-1" aria-labelledby="modalHapusFotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHapusFotoLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda yakin ingin menghapus Kegiatan: <strong x-text="deleteTitle"></strong>?</p>
                        <p class="text-danger small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
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
@endsection