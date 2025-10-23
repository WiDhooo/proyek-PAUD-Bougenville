@extends('layouts.app')

@section('title', 'Edit Profil Sekolah')

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.profil.index') }}" class="btn btn-light btn-sm border">
            <i class="bi bi-arrow-left"></i> Kembali ke Manajemen Konten
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Edit Profil Sekolah</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profil.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="visi" class="form-label fw-bold">Tentang Sekolah</label>
                    <textarea class="form-control" id="visi" name="visi" rows="3" required>{{ $profil['visi'] }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="misi" class="form-label fw-bold">Visi Misi Sekolah</label>
                    <textarea class="form-control" id="misi" name="misi" rows="5" required>{{ $profil['misi'] }}</textarea>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection