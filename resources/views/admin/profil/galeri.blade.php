@extends('layouts.app')

@section('title', 'Kelola Galeri')

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('admin.profil.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h3 class="fw-bold d-inline-block ms-3 mb-0">Kelola Galeri</h3>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahFoto">
            <i class="bi bi-plus-lg"></i> Tambah Foto Baru
        </button>
    </div>

    <div class="row row-cols-1 row-cols-md-4 g-4 mt-2">
        @forelse ($foto as $item)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="{{ $item['url'] }}" class="card-img-top" alt="{{ $item['judul'] }}">
                    <div class="card-body">
                        <p class="card-text">{{ $item['judul'] }}</p>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">Belum ada foto di galeri.</p>
            </div>
        @endforelse
    </div>
</div>

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
                        <label for="judul_foto" class="form-label">Judul/Keterangan Foto</label>
                        <input type="text" class="form-control" name="judul_foto" required>
                    </div>
                    <div class="mb-3">
                        <label for="file_foto" class="form-label">Pilih File Foto</label>
                        <input class="form-control" type="file" name="file_foto" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection