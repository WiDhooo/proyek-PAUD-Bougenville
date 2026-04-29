@extends('layouts.guru')

@section('title', 'Analisis dan Rekomendasi Minat Bakat — ' . $kelas->nama_kelas)

@section('content')
<div class="container-fluid" x-data="{
    searchQuery: '',
    get filteredRows() {
        if (!this.searchQuery) return null;
        const q = this.searchQuery.toLowerCase();
        document.querySelectorAll('tbody tr[data-name]').forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const nis  = row.dataset.nis.toLowerCase();
            row.style.display = (name.includes(q) || nis.includes(q)) ? '' : 'none';
        });
        return true;
    }
}" @keyup.window="filteredRows">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:0.88rem;">
            <li class="breadcrumb-item"><a href="{{ route('guru.rapor.pilih_kelas') }}" style="color: var(--paud-teal); text-decoration:none;">Analisis dan Rekomendasi Minat Bakat</a></li>
            <li class="breadcrumb-item active" style="color: var(--paud-muted);">{{ $kelas->nama_kelas }}</li>
        </ol>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: var(--paud-radius-sm); border: none;" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: var(--paud-radius-sm); border: none;" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Periode & Tahun Ajaran --}}
    <div class="paud-card mb-4">
        <div class="card-body py-3 px-4">
            <form action="{{ route('guru.rapor.daftar_siswa', $kelas->id) }}" method="GET" id="filter-rapor-form" class="d-flex align-items-center gap-3 flex-wrap">
                <span style="font-size:0.85rem; font-weight:600; color: var(--paud-muted);">
                    <i class="bi bi-funnel me-1"></i> Filter:
                </span>
                <select name="periode" class="form-select form-select-sm" style="width:120px; border-radius:20px; border-color: var(--paud-border); font-size:0.85rem;" onchange="this.form.submit()">
                    <option value="Ganjil" {{ $periode == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $periode == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
                <select name="tahun_ajaran" class="form-select form-select-sm" style="width:140px; border-radius:20px; border-color: var(--paud-border); font-size:0.85rem;" onchange="this.form.submit()">
                    @for($y = 2026; $y <= (int)date('Y') + 5; $y++)
                        @php $ta = $y . '/' . ($y + 1); @endphp
                        <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    {{-- Header + Search --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--paud-text);">
                <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">{{ $kelas->nama_kelas }}</span>
            </h4>
            <p style="color: var(--paud-muted); font-size:0.9rem; margin-left:15px;" class="mb-0">
                {{ $siswas->count() }} siswa | Periode: <strong>{{ $periode }}</strong> | TA: <strong>{{ $tahunAjaran }}</strong>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- Search Bar --}}
            <div class="input-group" style="width: 220px;">
                <span class="input-group-text border-0" style="background: var(--paud-card); border-radius: var(--paud-radius-sm) 0 0 var(--paud-radius-sm);">
                    <i class="bi bi-search" style="color: var(--paud-muted);"></i>
                </span>
                <input type="search" class="form-control border-0 shadow-none" placeholder="Cari siswa..."
                    x-model.debounce.300ms="searchQuery"
                    @input="filteredRows"
                    style="background: var(--paud-card); border-radius: 0 var(--paud-radius-sm) var(--paud-radius-sm) 0; font-size:0.88rem;">
            </div>
            {{-- Action Buttons --}}
            <a href="{{ route('guru.rapor.input', ['kelas_id' => $kelas->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
               class="btn paud-btn-outline btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Input Nilai
            </a>
            <form action="{{ route('guru.rapor.analisis') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                <button type="submit" class="btn paud-btn-primary btn-sm">
                    <i class="bi bi-stars me-1"></i> Generate Analisis AI
                </button>
            </form>
        </div>
    </div>

    {{-- Table Siswa --}}
    <div class="paud-card">
        <div class="p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="paud-thead">
                        <tr>
                            <th class="py-3 px-3" style="border-radius: var(--paud-radius-sm) 0 0 0; width:50px;">#</th>
                            <th class="py-3 px-3">Nama Siswa</th>
                            <th class="py-3 px-3">NIS</th>
                            <th class="py-3 px-3">L/P</th>
                            <th class="py-3 px-3 text-center">Status Analisis</th>
                            <th class="py-3 px-3 text-center" style="border-radius: 0 var(--paud-radius-sm) 0 0;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $i => $siswa)
                        @php
                            $analisis = $latestAnalisisMap->get($siswa->id);
                        @endphp
                        <tr class="paud-table-row" data-name="{{ $siswa->nama }}" data-nis="{{ $siswa->nis }}">
                            <td class="px-3 py-3" style="color: var(--paud-muted);">{{ $i + 1 }}</td>
                            <td class="px-3 py-3 fw-semibold" style="color: var(--paud-text);">{{ $siswa->nama }}</td>
                            <td class="px-3 py-3" style="color: var(--paud-muted);">{{ $siswa->nis }}</td>
                            <td class="px-3 py-3">
                                @if($siswa->jenis_kelamin == 'Laki-Laki')
                                    <span class="paud-badge" style="background: #E3F2FD; color: #1565C0;">L</span>
                                @else
                                    <span class="paud-badge" style="background: #FCE4EC; color: #C62828;">P</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($analisis)
                                    <span class="paud-badge bg-paud-green-light" style="color: var(--paud-green);">
                                        <i class="bi bi-check-circle-fill me-1" style="font-size:0.7rem;"></i> Cluster {{ $analisis->cluster_group }}
                                    </span>
                                @else
                                    <span class="paud-badge bg-paud-amber-light text-paud-amber">
                                        <i class="bi bi-hourglass-split me-1" style="font-size:0.7rem;"></i> Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('guru.rapor.detail', ['id' => $siswa->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                                       class="btn btn-sm paud-btn-outline" style="padding:5px 10px;" title="Lihat Rapor">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('guru.rapor.edit_nilai', ['id' => $siswa->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]) }}"
                                       class="btn btn-sm" style="padding:5px 10px; border:1.5px solid var(--paud-amber); color: var(--paud-amber); border-radius: var(--paud-radius-sm);" title="Edit Nilai">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('guru.rapor.destroy_nilai', $siswa->id) }}" method="POST" class="m-0"
                                        onsubmit="return confirm('Hapus SEMUA nilai {{ $siswa->nama }} untuk periode {{ $periode }} {{ $tahunAjaran }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="periode" value="{{ $periode }}">
                                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                                        <button type="submit" class="btn btn-sm" style="padding:5px 10px; border:1.5px solid var(--paud-coral); color: var(--paud-coral); border-radius: var(--paud-radius-sm);" title="Hapus Nilai">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5" style="color: var(--paud-muted);">
                                <i class="bi bi-people" style="font-size:2.5rem; color: var(--paud-border);"></i>
                                <p class="mt-2 mb-0">Belum ada siswa di kelas ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
