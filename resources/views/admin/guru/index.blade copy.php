@extends('layouts.app')

{{-- Ganti Judul Halaman --}}
@section('title', 'Manajemen Guru')

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card" x-data="{ 
        deleteName: '', 
        deleteUrl: '',
        editUrl: '',
        editData: {}
    }">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Guru</h5>
            {{-- Tombol dan Pencarian --}}
            <div class="d-flex">
                <form class="d-flex me-2">
                    <input class="form-control me-2" type="search" placeholder="Cari nama guru" aria-label="Search">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </form>
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                    <i class="bi bi-plus-lg"></i> Tambah Guru
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nama Guru</th>
                            <th scope="col">Jabatan</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Pendidikan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Looping Data Guru dari Route --}}
                        @forelse ($guru as $item)
                            <tr>
                                <td>{{ $item['nama'] }}</td>
                                <td>{{ $item['jabatan'] }}</td>
                                <td>{{ $item['alamat'] }}</td>
                                <td>{{ $item['pendidikan'] }}</td>
                                <td>
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditGuru"
                                    @click="
                                        editUrl = '{{ route('admin.guru.update', ['id' => $loop->iteration]) }}';
                                        editData = {{ json_encode($item) }};
                                    ">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapusGuru"
                                        @click="
                                            deleteName = '{{ $item['nama'] }}';
                                            deleteUrl = '{{ route('admin.guru.destroy', ['id' => $loop->iteration]) }}';
                                        ">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Data guru tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Nanti kita akan tambahkan pagination di sini --}}
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahGuru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTambahGuruLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalTambahGuruLabel">Tambah Guru Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form akan mengirim data ke route 'admin.guru.store' --}}
                <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4 text-center" x-data="{
                        imageData: '{{ asset('images/dashboard/blankImage.jpg') }}',
                        previewImage(event) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.imageData = e.target.result;
                            };
                            reader.readAsDataURL(event.target.files[0]);
                        }
                    }">
                        <img :src="imageData" class="img-thumbnail mb-3" alt="preview" style="width: 200px; height: 200px; object-fit: cover;">
                        <input type="file" class="form-control" name="image" @change="previewImage">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label fw-bold">Nama Guru</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Guru</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                    </div>

                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label fw-bold">Jabatan</label>
                            <select name="jabatan" class="form-select" required>
                                <option value="" selected>Pilih Jabatan</option>
                                <option value="Ketua Yayasan">Ketua Yayasan</option>
                                <option value="Kepala Sekolah">Kepala Sekolah</option>
                                <option value="Guru">Guru</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pendidikan" class="form-label fw-bold">Pendidikan</label>
                            <select name="pendidikan" class="form-select" required>
                                <option value="" selected>Pilih Pendidikan</option>
                                <option value="SMA">SMA</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="alamat" class="form-label fw-bold">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
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

<div class="modal fade" id="modalHapusGuru" tabindex="-1" aria-labelledby="modalHapusGuruLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalHapusGuruLabel">Konfirmasi Hapus</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus guru bernama <strong x-text="deleteName"></strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                {{-- Form ini akan diisi action-nya secara dinamis oleh Alpine.js --}}
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

<div class="modal fade" id="modalEditGuru" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalEditGuruLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalEditGuruLabel">Edit Data Guru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form ini action-nya akan diisi secara dinamis oleh Alpine.js --}}
                <form x-bind:action="editUrl" method="POST">
                    @csrf
                    @method('PUT') {{-- Wajib untuk route PUT di Laravel --}}

                    <div class="mb-3">
                        <label for="edit_nama" class="form-label fw-bold">Nama Guru</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" x-model="editData.nama" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_jabatan" class="form-label fw-bold">Jabatan</label>
                        <input type="text" class="form-control" id="edit_jabatan" name="jabatan" x-model="editData.jabatan" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label fw-bold">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3" x-model="editData.alamat"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_pendidikan" class="form-label fw-bold">Pendidikan</label>
                        <input type="text" class="form-control" id="edit_pendidikan" name="pendidikan" x-model="editData.pendidikan">
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
@endsection