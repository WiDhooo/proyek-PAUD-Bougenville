@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="container-fluid" x-data="{
    deleteName: '',
    deleteUrl: '',
    editUrl: '',
    editData: {}
}">
    {{-- Pesan Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Kelas</h5>
            <div class="d-flex">
                <form class="d-flex me-2">
                    <input class="form-control me-2" type="search" placeholder="Cari nama kelas" aria-label="Search">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                    <i class="bi bi-plus-lg"></i> Buat Kelas
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Kelas</th>
                            <th>Wali</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas as $item)
                            <tr>
                                <td>{{ $item['nama_kelas'] }}</td>
                                <td>{{ $item['kelas'] }}</td>
                                <td>{{ $item['wali'] }}</td>
                                <td>
                                <a href="{{ route('admin.kelas.show', ['id' => $item['id']]) }}" class="btn btn-info btn-sm text-white">
                                    <i class="bi bi-info-circle-fill"></i> Detail
                                </a>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditKelas"
                                        @click="
                                            editUrl = '{{ route('admin.kelas.update', ['id' => $item['id']]) }}';
                                            editData = {{ json_encode($item) }};
                                        ">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapusKelas"
                                        @click="
                                            deleteName = '{{ $item['nama_kelas'] }}';
                                            deleteUrl = '{{ route('admin.kelas.destroy', ['id' => $item['id']]) }}';
                                        ">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Data kelas tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahKelas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Kelas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.kelas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="nama_kelas" placeholder="Cth: Kelas Mandiri" required>
                        </div>
                        <div class="mb-3">
                            <label for="kelas" class="form-label">Tingkat</label>
                            <select name="kelas" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="wali" class="form-label">Wali Kelas</label>
                            <input type="text" class="form-control" name="wali" placeholder="Nama wali kelas" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditKelas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form x-bind:action="editUrl" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="nama_kelas" x-model="editData.nama_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tingkat</label>
                            <select name="kelas" class="form-select" x-model="editData.kelas" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                            </select>
                        </div>
                         <div class="mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <input type="text" class="form-control" name="wali" x-model="editData.wali" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHapusKelas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kelas <strong x-text="deleteName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <form x-bind:action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection