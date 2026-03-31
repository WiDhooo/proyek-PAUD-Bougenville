@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid" x-data="jadwalManager()">

    {{-- Greeting --}}
    <div class="mb-4">
        <h3 class="fw-bold" style="color: var(--paud-text);">
            Halo, {{ Auth::user()->name ?? 'Administrator' }}!
        </h3>
        <p style="color: var(--paud-muted);">Berikut adalah ringkasan aktivitas sekolah — {{ now()->translatedFormat('l,
            d F Y') }}</p>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show"
        style="border-radius: var(--paud-radius-sm); border:none;" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show"
        style="border-radius: var(--paud-radius-sm); border:none;" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="paud-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-paud-teal-light text-paud-teal me-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div style="font-size:0.82rem; color: var(--paud-muted); font-weight:500;">Total Siswa</div>
                        <div class="fw-bold fs-4" style="color: var(--paud-text);">{{ $data['total_siswa'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="paud-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-paud-amber-light text-paud-amber me-3">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                    <div>
                        <div style="font-size:0.82rem; color: var(--paud-muted); font-weight:500;">Total Guru</div>
                        <div class="fw-bold fs-4" style="color: var(--paud-text);">{{ $data['total_guru'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="paud-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-paud-green-light text-paud-green me-3">
                        <i class="bi bi-door-open-fill fs-4"></i>
                    </div>
                    <div>
                        <div style="font-size:0.82rem; color: var(--paud-muted); font-weight:500;">Total Kelas</div>
                        <div class="fw-bold fs-4" style="color: var(--paud-text);">{{ $data['total_kelas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Table --}}
    <div class="paud-card">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold mb-0" style="color: var(--paud-text);">
                    <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">
                        <i class="bi bi-calendar-week me-2" style="color: var(--paud-teal);"></i>Jadwal Mengajar
                    </span>
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn paud-btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
                    </button>
                    <button class="btn paud-btn-outline-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalHapusSemua">
                        <i class="bi bi-trash me-1"></i> Hapus Semua
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table text-center align-middle mb-0"
                    style="border-collapse: separate; border-spacing: 0;">
                    <thead class="paud-thead">
                        <tr>
                            <th style="border-radius: var(--paud-radius-sm) 0 0 0; min-width:90px;">Hari</th>
                            @foreach($kelasList as $kelas)
                            <th>{{ $kelas->nama_kelas }}<br><small class="fw-normal opacity-75">{{ $kelas->kelas
                                    }}</small></th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['jadwal'] as $hari => $kelasData)
                        <tr class="paud-table-row">
                            <td class="fw-semibold"
                                style="color: var(--paud-text); border-right: 1px solid var(--paud-border);">
                                {{ $hari }}
                            </td>
                            @foreach($kelasData as $namaKelas => $items)
                            <td style="border: 1px solid var(--paud-border);">
                                @forelse($items as $item)
                                <div class="d-inline-flex flex-column align-items-center mb-2 px-2 py-1"
                                    style="background: var(--paud-teal-light); border-radius: var(--paud-radius-sm); min-width: 110px;">
                                    <span class="fw-semibold" style="font-size:0.82rem; color: var(--paud-text);">{{
                                        $item['guru'] }}</span>
                                    <small style="color: var(--paud-muted); font-size:0.75rem;">{{ $item['waktu']
                                        }}</small>
                                    <div class="d-flex gap-1 mt-1">
                                        <button class="btn btn-sm"
                                            style="padding:2px 8px; border: 1.5px solid var(--paud-amber); color: var(--paud-amber); border-radius: 6px; font-size:0.75rem;"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit"
                                            @click="prepareEdit({{ json_encode($item['data']) }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm"
                                            style="padding:2px 8px; border: 1.5px solid var(--paud-coral); color: var(--paud-coral); border-radius: 6px; font-size:0.75rem;"
                                            data-bs-toggle="modal" data-bs-target="#modalHapus"
                                            @click="prepareDelete({{ json_encode($item['data']) }}, '{{ $item['guru'] }}')"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @empty
                                <span style="color: var(--paud-border); font-size:1.2rem;">—</span>
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

    {{-- ======================== MODAL TAMBAH ======================== --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.jadwal.store') }}" method="POST" @submit="submitAdd($event)">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"
                                style="color:var(--paud-teal);"></i>Tambah Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" x-model="addData.hari"
                                :class="{'is-invalid': addErrors.hari}" @change="validateAdd('hari')">
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
                            <select name="kelas_id" class="form-select" x-model="addData.kelas_id"
                                :class="{'is-invalid': addErrors.kelas_id}" @change="validateAdd('kelas_id')">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} — {{ $kelas->kelas }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="addErrors.kelas_id"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-select" x-model="addData.guru_id"
                                :class="{'is-invalid': addErrors.guru_id}" @change="validateAdd('guru_id')">
                                <option value="">Pilih Guru</option>
                                @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="addErrors.guru_id"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_mulai" class="form-control" x-model="addData.waktu_mulai"
                                    :class="{'is-invalid': addErrors.waktu_mulai}" @input="validateAdd('waktu_mulai')">
                                <div class="invalid-feedback" x-text="addErrors.waktu_mulai"></div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_selesai" class="form-control"
                                    x-model="addData.waktu_selesai" :class="{'is-invalid': addErrors.waktu_selesai}"
                                    @input="validateAdd('waktu_selesai')">
                                <div class="invalid-feedback" x-text="addErrors.waktu_selesai"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ======================== MODAL EDIT ======================== --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form :action="editUrl" method="POST" @submit="submitEdit($event)">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"
                                style="color:var(--paud-amber);"></i>Edit Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Hari <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" x-model="editData.hari"
                                :class="{'is-invalid': editErrors.hari}" @change="validateEdit('hari')">
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
                            <select name="kelas_id" class="form-select" x-model="editData.kelas_id"
                                :class="{'is-invalid': editErrors.kelas_id}" @change="validateEdit('kelas_id')">
                                @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} — {{ $kelas->kelas }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="editErrors.kelas_id"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" class="form-select" x-model="editData.guru_id"
                                :class="{'is-invalid': editErrors.guru_id}" @change="validateEdit('guru_id')">
                                @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="editErrors.guru_id"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_mulai" class="form-control"
                                    x-model="editData.waktu_mulai" :class="{'is-invalid': editErrors.waktu_mulai}"
                                    @input="validateEdit('waktu_mulai')">
                                <div class="invalid-feedback" x-text="editErrors.waktu_mulai"></div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_selesai" class="form-control"
                                    x-model="editData.waktu_selesai" :class="{'is-invalid': editErrors.waktu_selesai}"
                                    @input="validateEdit('waktu_selesai')">
                                <div class="invalid-feedback" x-text="editErrors.waktu_selesai"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-trash me-2"
                                style="color:var(--paud-coral);"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p style="color: var(--paud-text);">
                            Yakin ingin menghapus jadwal guru <strong x-text="deleteInfo"></strong>?
                        </p>
                        <p class="mb-0 text-paud-coral" style="font-size:0.88rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i>Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-danger btn-sm">
                            <i class="bi bi-trash me-1"></i> Ya, Hapus
                        </button>
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
                    <div class="modal-header" style="background: var(--paud-coral-light);">
                        <h5 class="modal-title text-paud-coral">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Semua Jadwal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div
                            class="paud-badge bg-paud-coral-light text-paud-coral d-inline-flex align-items-center gap-1 mb-3">
                            <i class="bi bi-shield-exclamation"></i> Peringatan Kritikal
                        </div>
                        <p style="color: var(--paud-text);">Anda akan menghapus <strong>semua jadwal</strong> yang ada.
                            Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn paud-btn-outline btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn paud-btn-danger btn-sm">
                            <i class="bi bi-trash me-1"></i> Ya, Hapus Semua
                        </button>
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

            validateAdd(field) {
                const data = this.addData;
                const err = this.addErrors;
                if (field === 'hari') err.hari = data.hari ? '' : 'Hari wajib dipilih.';
                if (field === 'kelas_id') err.kelas_id = data.kelas_id ? '' : 'Kelas wajib dipilih.';
                if (field === 'guru_id') err.guru_id = data.guru_id ? '' : 'Guru wajib dipilih.';
                if (field === 'waktu_mulai') err.waktu_mulai = data.waktu_mulai ? '' : 'Jam mulai wajib diisi.';
                if (field === 'waktu_selesai' || field === 'waktu_mulai') {
                    if (!data.waktu_selesai) {
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
                if (Object.values(this.addErrors).some(val => val !== '')) e.preventDefault();
            },

            prepareEdit(item) {
                this.editData = {
                    id: item.id,
                    hari: item.hari,
                    kelas_id: item.kelas_id,
                    guru_id: item.guru_id,
                    waktu_mulai: item.waktu_mulai ? item.waktu_mulai.substring(0, 5) : '',
                    waktu_selesai: item.waktu_selesai ? item.waktu_selesai.substring(0, 5) : ''
                };
                this.editUrl = `/admin/jadwal/${item.id}`;
                this.editErrors = { hari: '', kelas_id: '', guru_id: '', waktu_mulai: '', waktu_selesai: '' };
            },
            validateEdit(field) {
                const data = this.editData;
                const err = this.editErrors;
                if (field === 'hari') err.hari = data.hari ? '' : 'Hari wajib dipilih.';
                if (field === 'kelas_id') err.kelas_id = data.kelas_id ? '' : 'Kelas wajib dipilih.';
                if (field === 'guru_id') err.guru_id = data.guru_id ? '' : 'Guru wajib dipilih.';
                if (field === 'waktu_mulai') err.waktu_mulai = data.waktu_mulai ? '' : 'Jam mulai wajib diisi.';
                if (field === 'waktu_selesai' || field === 'waktu_mulai') {
                    if (!data.waktu_selesai) {
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
                if (Object.values(this.editErrors).some(val => val !== '')) e.preventDefault();
            },

            prepareDelete(item, guruName) {
                this.deleteUrl = `/admin/jadwal/${item.id}`;
                this.deleteInfo = guruName;
            }
        }
    }
</script>
@endsection