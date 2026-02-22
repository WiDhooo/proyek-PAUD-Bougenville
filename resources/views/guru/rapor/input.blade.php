@extends('layouts.guru')

@section('title', 'Input Nilai Rapor Digital')

@section('content')
<div class="container-fluid">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.rapor.pilih_kelas') }}">Rapor Digital</a></li>
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
            <h5 class="fw-bold mb-4">
                <i class="bi bi-pencil-square text-primary me-2"></i> Form Input Nilai
            </h5>

            <form action="{{ route('guru.rapor.store') }}" method="POST" id="formInputNilai">
                @csrf
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nama Siswa</label>
                        <select name="siswa_id" class="form-select" required id="selectSiswa">
                            @foreach($siswas as $siswa)
                                @php $sudahDinilai = in_array($siswa->id, $siswaYangSudahDinilai ?? []); @endphp
                                <option value="{{ $siswa->id }}" data-sudah-dinilai="{{ $sudahDinilai ? '1' : '0' }}">
                                    {{ $siswa->nama }} {{ $sudahDinilai ? '✅ (Sudah Dinilai)' : '' }}
                                </option>
                            @endforeach
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
                                        @foreach([1 => 'BB', 2 => 'MB', 3 => 'BSH', 4 => 'BSB'] as $skor => $label)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="nilai[{{ $aspek->id }}]" value="{{ $skor }}" id="aspek{{ $aspek->id }}_{{ $skor }}" required>
                                                <label class="form-check-label small" for="aspek{{ $aspek->id }}_{{ $skor }}">{{ $label }}</label>
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

            <hr class="my-4">

            <h5 class="fw-bold mb-3">🤖 Aksi Lanjutan</h5>
            <form action="{{ route('guru.rapor.analisis') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                
                <button type="submit" class="btn btn-primary rounded-3">
                    🤖 Generate Analisis AI untuk Kelas Ini
                </button>
                <p class="text-muted small mt-2">*Pastikan semua siswa telah dinilai sebelum menjalankan analisis.</p>
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
            const nama = selected.textContent.replace('✅ (Sudah Dinilai)', '').trim();
            if (!confirm('⚠️ ' + nama + ' sudah memiliki nilai di periode ini.\n\nNilai LAMA akan DITIMPA dengan nilai baru.\nApakah Anda yakin ingin melanjutkan?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
