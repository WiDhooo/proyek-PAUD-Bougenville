@extends('layouts.app')

@section('title', 'Manajemen Konten Website')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3>Manajemen Konten Website</h3>
        <p class="text-muted">Pilih menu di bawah untuk mengelola konten pada halaman depan website.</p>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <a href="{{ route('admin.profil.edit') }}" class="card h-100 border-0 shadow-sm text-decoration-none text-dark">
                <div class="card-body text-center p-4">
                    <i class="bi bi-person-vcard fs-1 text-primary mb-3"></i>
                    <h4 class="card-title">Kelola Profil Sekolah</h4>
                    <p class="card-text text-muted">Ubah Visi, Misi, Sejarah, dan informasi kontak sekolah.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6 mb-4">
            <a href="{{ route('admin.galeri.index') }}" class="card h-100 border-0 shadow-sm text-decoration-none text-dark">
                <div class="card-body text-center p-4">
                    <i class="bi bi-images fs-1 text-success mb-3"></i>
                    <h4 class="card-title">Kelola Galeri</h4>
                    <p class="card-text text-muted">Tambah atau hapus koleksi foto dan video kegiatan sekolah.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection