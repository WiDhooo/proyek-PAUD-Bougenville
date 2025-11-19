@extends('layouts.app')

@section('title', 'Edit Profil Sekolah')

@section('content')

<div class="container-fluid">

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('admin.profil.index') }}" class="btn btn-light btn-sm border">
            <i class="bi bi-arrow-left"></i> Kembali ke Manajemen Konten
        </a>
    </div>

    <div class="card border-0 shadow-sm" x-data="profilData()">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">Form Edit Profil Sekolah</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profil.update', $profil->id) }}" method="POST"> 
                @csrf
                @method('PUT') 
                
                {{-- 1. INPUT TENTANG SEKOLAH --}}
                <div class="mb-5">
                    <label for="tentang_sekolah" class="form-label fw-bold">Tentang Sekolah</label>
                    <textarea class="form-control" id="tentang_sekolah" name="tentang_sekolah" rows="5" required>{{ old('tentang_sekolah', $profil->tentang_sekolah) }}</textarea>
                    <small class="text-muted">Deskripsi singkat sekolah.</small>
                    @error('tentang_sekolah')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                
                <hr>

                {{-- 2. INPUT POIN VISI --}}
                <div class="mb-5">
                    <h5 class="fw-bold text-primary mb-3">Poin Visi Sekolah</h5>

                    <template x-for="(visi, index) in visiItems" :key="index">
                        <div class="input-group mb-2">
                            <span class="input-group-text fw-bold text-muted" x-text="index + 1"></span>
                            
                            <input type="text" 
                                    class="form-control" 
                                    placeholder="Masukkan satu poin visi..."
                                    :name="`vision[${index}][isi]`" 
                                    x-model="visi.isi" 
                                    required>
                            
                            <button type="button" 
                                    @click="visiItems.splice(index, 1)" 
                                    class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </template>
                    @error('vision')<div class="text-danger">Poin Visi wajib diisi setidaknya satu.</div>@enderror
                    @error('vision.*.isi')<div class="text-danger">Setiap poin Visi wajib diisi.</div>@enderror

                    <button type="button" 
                            @click="visiItems.push({isi: ''})" 
                            class="btn btn-outline-primary btn-sm mt-2">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Poin Visi
                    </button>
                </div>

                <hr>

                {{-- 3. INPUT POIN MISI --}}
                <div class="mb-5">
                    <h5 class="fw-bold text-primary mb-3">Poin Misi Sekolah</h5>

                    <template x-for="(misi, index) in misiItems" :key="index">
                        <div class="input-group mb-2">
                            <span class="input-group-text fw-bold text-muted" x-text="index + 1"></span>
                            
                            <input type="text" 
                                    class="form-control" 
                                    placeholder="Masukkan satu poin misi..."
                                    :name="`mission[${index}][isi]`" 
                                    x-model="misi.isi" 
                                    required>
                            
                            <button type="button" 
                                    @click="misiItems.splice(index, 1)" 
                                    class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </template>
                    @error('mission')<div class="text-danger">Poin Misi wajib diisi setidaknya satu.</div>@enderror
                    @error('mission.*.isi')<div class="text-danger">Setiap poin Misi wajib diisi.</div>@enderror

                    <button type="button" 
                            @click="misiItems.push({isi: ''})" 
                            class="btn btn-outline-primary btn-sm mt-2">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Poin Misi
                    </button>
                </div>

                {{-- Tombol Simpan --}}
                <div class="d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function profilData() {
    return {
        visiItems: {!! json_encode($visi->map(function($item) {
            return ['isi' => $item->isi];
        })->toArray()) !!},
        misiItems: {!! json_encode($misi->map(function($item) {
            return ['isi' => $item->isi];
        })->toArray()) !!}
    }
}
</script>
@endsection