@extends('layouts.app')

@section('title', 'Manajemen Murid')

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
            <h5 class="card-title mb-0">Data Murid</h5>
            <div class="d-flex">
                <form class="d-flex me-2">
                    <input class="form-control me-2" type="search" placeholder="Cari Nama Murid..." aria-label="Search">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTambahMurid">
                    <i class="bi bi-plus-lg"></i> Tambah Murid
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Murid</th>
                            <th>Usia</th>
                            <th>Jenis Kelamin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($murid as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item['nik'] }}</td>
                                <td>{{ $item['nama'] }}</td>
                                <td>{{ $item['usia'] }}</td>
                                <td>{{ $item['jenis_kelamin'] }}</td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm text-white">Detail</a>
                                    <button type="button" class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditMurid"
                                        @click="
                                            editUrl = '{{ route('admin.murid.update', ['id' => $item['id']]) }}';
                                            editData = {{ json_encode($item) }};
                                        ">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapusMurid"
                                        @click="
                                            deleteName = '{{ $item['nama'] }}';
                                            deleteUrl = '{{ route('admin.murid.destroy', ['id' => $item['id']]) }}';
                                        ">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Data murid tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahMurid" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Murid Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form action="{{ route('admin.murid.store') }}" method="POST">
                        @csrf
                        <div class="mb-3"><label class="form-label">NIK</label><input type="text" class="form-control" name="nik" required></div>
                        <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" name="nama" required></div>
                        <div class="mb-3"><label class="form-label">Usia</label><input type="text" class="form-control" name="usia" required></div>
                        <div class="mb-3"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditMurid" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Murid</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form x-bind:action="editUrl" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3"><label class="form-label">NIK</label><input type="text" class="form-control" name="nik" x-model="editData.nik" required></div>
                        <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" name="nama" x-model="editData.nama" required></div>
                        <div class="mb-3"><label class="form-label">Usia</label><input type="text" class="form-control" name="usia" x-model="editData.usia" required></div>
                        <div class="mb-3"><label class="form-label">Jenis Kelamin</label><select name="jenis_kelamin" class="form-select" x-model="editData.jenis_kelamin" required><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHapusMurid" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><p>Apakah Anda yakin ingin menghapus murid bernama <strong x-text="deleteName"></strong>?</p></div>
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