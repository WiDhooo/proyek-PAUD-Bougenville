@extends('layouts.guru')

@section('title', 'Input Nilai Analisis dan Rekomendasi Minat Bakat')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.rapor.pilih_kelas') }}">Analisis dan Rekomendasi Minat Bakat</a></li>
            @if(isset($kelasId) && $kelasId)
                <li class="breadcrumb-item"><a href="{{ route('guru.rapor.daftar_siswa', ['id' => $kelasId, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}">Daftar Siswa</a></li>
            @endif
            <li class="breadcrumb-item active">Input Nilai</li>
        </ol>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pilih Kelas --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('guru.rapor.input') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pilih Kelas</label>
                    <select name="kelas_id" class="form-select">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary rounded-3 w-100">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($siswas) && $siswas->count() > 0)
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-pencil-square text-primary me-2"></i> Form Input Nilai
            </h5>

            <div class="alert alert-info rounded-3 mb-4 border-0" style="background-color: #f8fbff; border-left: 4px solid #0d6efd !important;">
                <h6 class="fw-bold mb-2 text-primary" style="font-size: 0.9rem;"><i class="bi bi-info-circle me-1"></i> Keterangan Skala Penilaian:</h6>
                <div class="row g-2 small text-dark">
                    <div class="col-md-3"><strong>BB</strong>: Belum Berkembang (Skor 1)</div>
                    <div class="col-md-3"><strong>MB</strong>: Mulai Berkembang (Skor 2)</div>
                    <div class="col-md-3"><strong>BSH</strong>: Berkembang Sesuai Harapan (Skor 3)</div>
                    <div class="col-md-3"><strong>BSB</strong>: Berkembang Sangat Baik (Skor 4)</div>
                </div>
            </div>

            <form action="{{ route('guru.rapor.store') }}" method="POST" id="formInputNilai">
                @csrf
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nama Siswa</label>
                        <select name="siswa_id" class="form-select" required id="selectSiswa">
                            <option value="">-- Pilih Siswa --</option>
                            <optgroup label="Belum Dinilai">
                                @foreach($siswas as $siswa)
                                    @if(!in_array($siswa->id, $siswaYangSudahDinilai ?? []))
                                        <option value="{{ $siswa->id }}" data-sudah-dinilai="0">
                                            {{ $siswa->nama }}
                                        </option>
                                    @endif
                                @endforeach
                            </optgroup>
                            <optgroup label="Sudah Dinilai (Selesai)">
                                @foreach($siswas as $siswa)
                                    @if(in_array($siswa->id, $siswaYangSudahDinilai ?? []))
                                        <option value="{{ $siswa->id }}" data-sudah-dinilai="1">
                                            {{ $siswa->nama }}
                                        </option>
                                    @endif
                                @endforeach
                            </optgroup>
                        </select>
                        <div id="warningOverwrite" class="text-warning small mt-1 d-none">
                            <i class="bi bi-exclamation-triangle me-1"></i> Siswa ini sudah memiliki nilai. Data lama akan <strong>ditimpa</strong> jika disimpan.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Periode</label>
                        <div class="form-control bg-light">{{ $periode }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tahun Ajaran</label>
                        <div class="form-control bg-light">{{ $tahunAjaran }}</div>
                    </div>
                </div>

                <hr>

                @foreach($aspekPenilaians as $lingkup => $aspeks)
                    <h6 class="fw-bold text-primary mt-4 mb-3">
                        <i class="bi bi-bookmark-fill me-1"></i> {{ $lingkup }}
                    </h6>
                    <div class="row g-3">
                        @foreach($aspeks as $aspek)
                            <div class="col-md-6">
                                <div class="card bg-light border-0 rounded-3 p-3">
                                    <p class="fw-semibold small mb-2">{{ $aspek->sub_lingkup }} — {{ $aspek->indikator }}</p>
                                    <div class="d-flex gap-3">
                                        @foreach([1, 2, 3, 4] as $skor)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="nilai[{{ $aspek->id }}]" value="{{ $skor }}" id="aspek{{ $aspek->id }}_{{ $skor }}" required>
                                                <label class="form-check-label" for="aspek{{ $aspek->id }}_{{ $skor }}">{{ $skor }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success btn-lg rounded-3">
                        <i class="bi bi-check-circle me-1"></i> Simpan Nilai
                    </button>
                </div>
            </form>


        </div>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectSiswa = document.getElementById('selectSiswa');
    const warning = document.getElementById('warningOverwrite');
    const form = document.getElementById('formInputNilai');

    if (!selectSiswa || !form) return;

    function checkSudahDinilai() {
        const selected = selectSiswa.selectedOptions[0];
        if (selected && selected.dataset.sudahDinilai === '1') {
            warning.classList.remove('d-none');
        } else {
            warning.classList.add('d-none');
        }
    }

    selectSiswa.addEventListener('change', checkSudahDinilai);
    checkSudahDinilai();

    form.addEventListener('submit', function(e) {
        const selected = selectSiswa.selectedOptions[0];
        if (selected && selected.dataset.sudahDinilai === '1') {
            const nama = selected.textContent.trim();
            if (!confirm('⚠️ ' + nama + ' sudah memiliki nilai di periode ini.\n\nNilai LAMA akan DITIMPA dengan nilai baru.\nApakah Anda yakin ingin melanjutkan?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
