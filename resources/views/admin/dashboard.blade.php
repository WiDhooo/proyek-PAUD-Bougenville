@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid" x-data="jadwalManager()">
    <div class="mb-4">
        <h3>Selamat Datang, {{ Auth::user()->name ?? 'Administrator' }}! 👋</h3>
        <p class="text-muted">Berikut adalah ringkasan aktivitas sekolah hari ini.</p>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards (Sama seperti sebelumnya) --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-3 me-4"><i class="bi bi-people-fill fs-2 text-primary"></i></div>
                    <div><p class="text-muted mb-1">Total Siswa</p><h4 class="fw-bold mb-0">{{ $data['total_siswa'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 me-4"><i class="bi bi-person-badge-fill fs-2 text-success"></i></div>
                    <div><p class="text-muted mb-1">Total Guru</p><h4 class="fw-bold mb-0">{{ $data['total_guru'] }}</h4></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-warning bg-opacity-10 rounded-3 me-4"><i class="bi bi-house-door-fill fs-2 text-warning"></i></div>
                    <div><p class="text-muted mb-1">Total Kelas</p><h4 class="fw-bold mb-0">{{ $data['total_kelas'] }}</h4></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Jadwal Mengajar</h5>
                        <div>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="bi bi-plus-lg"></i> Tambah Jadwal
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapusSemua">
                                <i class="bi bi-trash-fill"></i> Hapus Semua
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%;">Hari</th>
                                    @foreach($kelasList as $kelas)
                                        <th>{{ $kelas->nama_kelas }} - {{ $kelas->kelas }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['jadwal'] as $hari => $kelasData)
                                <tr>
                                    <td class="fw-bold">{{ $hari }}</td>
                                    @foreach($kelasData as $namaKelas => $items)
                                        <td>
                                            @forelse($items as $item)
                                                <div class="d-flex flex-column align-items-center mb-2 p-1 border rounded bg-light">
                                                    <span class="fw-bold small">{{ $item['guru'] }}</span>
                                                    <small class="text-muted">{{ $item['waktu'] }}</small>
                                                    <div class="mt-1">
                                                        {{-- Tombol Edit --}}
                                                        <button class="btn btn-xs btn-warning py-0 px-1" 
                                                            data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                            @click="prepareEdit({{ json_encode($item['data']) }})">
                                                            <i class="bi bi-pencil" style="font-size: 0.7rem;"></i>
                                                        </button>
                                                        {{-- Tombol Hapus --}}
                                                        <button class="btn btn-xs btn-danger py-0 px-1" 
                                                            data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                            @click="prepareDelete({{ json_encode($item['data']) }}, '{{ $item['guru'] }}')">
                                                            <i class="bi bi-trash" style="font-size: 0.7rem;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================== MODAL TAMBAH ======================== --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.jadwal.store') }}" method="POST" @submit="submitAdd($event)">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" x-model="addData.hari" :class="{'is-invalid': addErrors.hari}" @change="validateAdd('hari')">
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                            <div class="invalid-feedback" x-text="addErrors.hari"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" class="form-select" x-model="addData.kelas_id" :class="{'is-invalid': addErrors.kelas_id}" @change="validateAdd('kelas_id')">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} - {{ $kelas->kelas }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="addErrors.kelas_id"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-select" x-model="addData.guru_id" :class="{'is-invalid': addErrors.guru_id}" @change="validateAdd('guru_id')">
                                <option value="">Pilih Guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="addErrors.guru_id"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_mulai" class="form-control" 
                                           x-model="addData.waktu_mulai" 
                                           :class="{'is-invalid': addErrors.waktu_mulai}" 
                                           @input="validateAdd('waktu_mulai')">
                                    <div class="invalid-feedback" x-text="addErrors.waktu_mulai"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_selesai" class="form-control" 
                                           x-model="addData.waktu_selesai" 
                                           :class="{'is-invalid': addErrors.waktu_selesai}" 
                                           @input="validateAdd('waktu_selesai')">
                                    <div class="invalid-feedback" x-text="addErrors.waktu_selesai"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ======================== MODAL EDIT ======================== --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="editUrl" method="POST" @submit="submitEdit($event)">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" x-model="editData.hari" :class="{'is-invalid': editErrors.hari}" @change="validateEdit('hari')">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                            </select>
                            <div class="invalid-feedback" x-text="editErrors.hari"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" class="form-select" x-model="editData.kelas_id" :class="{'is-invalid': editErrors.kelas_id}" @change="validateEdit('kelas_id')">
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} - {{ $kelas->kelas }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="editErrors.kelas_id"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-select" x-model="editData.guru_id" :class="{'is-invalid': editErrors.guru_id}" @change="validateEdit('guru_id')">
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="editErrors.guru_id"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_mulai" class="form-control" x-model="editData.waktu_mulai" :class="{'is-invalid': editErrors.waktu_mulai}" @input="validateEdit('waktu_mulai')">
                                    <div class="invalid-feedback" x-text="editErrors.waktu_mulai"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="waktu_selesai" class="form-control" x-model="editData.waktu_selesai" :class="{'is-invalid': editErrors.waktu_selesai}" @input="validateEdit('waktu_selesai')">
                                    <div class="invalid-feedback" x-text="editErrors.waktu_selesai"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus (Standar) --}}
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus jadwal guru <strong x-text="deleteInfo"></strong>?</p>
                        <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus Semua --}}
    <div class="modal fade" id="modalHapusSemua" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.jadwal.destroyAll') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Semua Jadwal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger mb-3"><strong>Peringatan!</strong> Anda akan menghapus SEMUA jadwal.</div>
                        <p>Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus Semua</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function jadwalManager() {
        return {
            // STATE TAMBAH
            addData: { hari: '', kelas_id: '', guru_id: '', waktu_mulai: '', waktu_selesai: '' },
            addErrors: { hari: '', kelas_id: '', guru_id: '', waktu_mulai: '', waktu_selesai: '' },

            // STATE EDIT
            editUrl: '',
            editData: { id: null, hari: '', kelas_id: '', guru_id: '', waktu_mulai: '', waktu_selesai: '' },
            editErrors: { hari: '', kelas_id: '', guru_id: '', waktu_mulai: '', waktu_selesai: '' },

            // STATE DELETE
            deleteUrl: '',
            deleteInfo: '',

            // --- VALIDASI TAMBAH ---
            validateAdd(field) {
                const data = this.addData;
                const err = this.addErrors;

                if(field === 'hari') err.hari = data.hari ? '' : 'Hari wajib dipilih.';
                if(field === 'kelas_id') err.kelas_id = data.kelas_id ? '' : 'Kelas wajib dipilih.';
                if(field === 'guru_id') err.guru_id = data.guru_id ? '' : 'Guru wajib dipilih.';
                if(field === 'waktu_mulai') err.waktu_mulai = data.waktu_mulai ? '' : 'Jam mulai wajib diisi.';
                
                // Logic Jam Selesai > Jam Mulai
                if(field === 'waktu_selesai' || field === 'waktu_mulai') {
                    if(!data.waktu_selesai) {
                        err.waktu_selesai = 'Jam selesai wajib diisi.';
                    } else if (data.waktu_mulai && data.waktu_selesai <= data.waktu_mulai) {
                        err.waktu_selesai = 'Jam selesai harus lebih besar dari jam mulai.';
                    } else {
                        err.waktu_selesai = '';
                    }
                }
            },
            submitAdd(e) {
                ['hari', 'kelas_id', 'guru_id', 'waktu_mulai', 'waktu_selesai'].forEach(f => this.validateAdd(f));
                if(Object.values(this.addErrors).some(val => val !== '')) e.preventDefault();
            },

            // --- VALIDASI EDIT ---
            prepareEdit(item) {
                this.editData = {
                    id: item.id,
                    hari: item.hari,
                    kelas_id: item.kelas_id,
                    guru_id: item.guru_id,
                    // Ambil 5 karakter pertama (HH:mm) untuk input type="time"
                    waktu_mulai: item.waktu_mulai ? item.waktu_mulai.substring(0, 5) : '',
                    waktu_selesai: item.waktu_selesai ? item.waktu_selesai.substring(0, 5) : ''
                };
                this.editUrl = `/admin/jadwal/${item.id}`;
                this.editErrors = { hari: '', kelas_id: '', guru_id: '', waktu_mulai: '', waktu_selesai: '' };
            },
            validateEdit(field) {
                const data = this.editData;
                const err = this.editErrors;

                if(field === 'hari') err.hari = data.hari ? '' : 'Hari wajib dipilih.';
                if(field === 'kelas_id') err.kelas_id = data.kelas_id ? '' : 'Kelas wajib dipilih.';
                if(field === 'guru_id') err.guru_id = data.guru_id ? '' : 'Guru wajib dipilih.';
                if(field === 'waktu_mulai') err.waktu_mulai = data.waktu_mulai ? '' : 'Jam mulai wajib diisi.';
                
                if(field === 'waktu_selesai' || field === 'waktu_mulai') {
                    if(!data.waktu_selesai) {
                        err.waktu_selesai = 'Jam selesai wajib diisi.';
                    } else if (data.waktu_mulai && data.waktu_selesai <= data.waktu_mulai) {
                        err.waktu_selesai = 'Jam selesai harus lebih besar dari jam mulai.';
                    } else {
                        err.waktu_selesai = '';
                    }
                }
            },
            submitEdit(e) {
                ['hari', 'kelas_id', 'guru_id', 'waktu_mulai', 'waktu_selesai'].forEach(f => this.validateEdit(f));
                if(Object.values(this.editErrors).some(val => val !== '')) e.preventDefault();
            },

            // --- LOGIC HAPUS ---
            prepareDelete(item, guruName) {
                this.deleteUrl = `/admin/jadwal/${item.id}`;
                this.deleteInfo = guruName;
            }
        }
    }
</script>
@endsection