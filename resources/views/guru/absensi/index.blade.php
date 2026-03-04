@extends('layouts.guru')

@section('title', 'Absensi')

@section('content')
<div class="container-fluid" x-data="absensiManager()">

    {{-- Filter Periode --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('guru.absensi.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-semibold mb-0">Periode:</label>
                <select name="periode" class="form-select form-select-sm" style="width:130px;" onchange="this.form.submit()">
                    <option value="Ganjil" {{ $periode == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $periode == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
                <label class="fw-semibold mb-0">Tahun Ajaran:</label>
                <select name="tahun_ajaran" class="form-select form-select-sm" style="width:150px;" onchange="this.form.submit()">
                    @for($y = 2026; $y <= (int)date('Y') + 5; $y++)
                        <option value="{{ $y }}/{{ $y+1 }}" {{ $tahunAjaran == "$y/".($y+1) ? 'selected' : '' }}>
                            {{ $y }}/{{ $y+1 }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    {{-- Tabel Jadwal —— style Data Siswa --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Jadwal Mengajar Anda</h5>
            <div class="input-group" style="width: 300px;">
                <input type="search" class="form-control" placeholder="Cari kelas atau hari..." x-model.debounce.300ms="searchQuery">
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th @click="sortBy('kelas')" style="cursor:pointer;">
                                Kelas
                                <span x-show="sortColumn === 'kelas'">
                                    <i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i>
                                </span>
                            </th>
                            <th @click="sortBy('hari')" style="cursor:pointer;">
                                Hari
                                <span x-show="sortColumn === 'hari'">
                                    <i :class="sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"></i>
                                </span>
                            </th>
                            <th>Jam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr>
                                <td x-text="(currentPage - 1) * itemsPerPage + index + 1"></td>
                                <td x-text="item.kelas_nama"></td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1" x-text="item.hari"></span>
                                </td>
                                <td x-text="item.jam"></td>
                                <td>
                                    <a :href="item.kalender_url" class="btn btn-success btn-sm text-white">
                                        <i class="bi bi-calendar2-week"></i> Buka Kalender
                                    </a>
                                    <a :href="item.rekap_url" class="btn btn-outline-primary btn-sm ms-1">
                                        <i class="bi bi-clipboard-data"></i> Rekap
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada jadwal ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <nav x-show="totalPages > 1" class="d-flex justify-content-end mt-3">
                <ul class="pagination">
                    <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                        <a class="page-link" href="#" @click.prevent="currentPage--">Previous</a>
                    </li>
                    <template x-for="page in totalPages" :key="page">
                        <li class="page-item" :class="{ 'active': currentPage === page }">
                            <a class="page-link" href="#" @click.prevent="currentPage = page" x-text="page"></a>
                        </li>
                    </template>
                    <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                        <a class="page-link" href="#" @click.prevent="currentPage++">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@php
$jadwalData = $jadwals->map(function($j) use ($periode, $tahunAjaran) {
    return [
        'id'          => $j->id,
        'kelas_nama'  => ($j->kelas->nama_kelas ?? '-') . ' (' . ($j->kelas->kelas ?? '') . ')',
        'hari'        => $j->hari,
        'jam'         => $j->waktu_mulai && $j->waktu_selesai
            ? \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i')
            : '-',
        'kalender_url' => route('guru.absensi.kalender', ['id' => $j->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]),
        'rekap_url'    => route('guru.absensi.rekap',    ['id' => $j->id, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran]),
    ];
})->values();
@endphp

<script>
function absensiManager() {
    return {
        searchQuery: '',
        sortColumn: 'hari',
        sortDirection: 'asc',
        currentPage: 1,
        itemsPerPage: 10,

        items: @json($jadwalData),

        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
        },

        get filteredItems() {
            let filtered = [...this.items];
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(i =>
                    i.kelas_nama.toLowerCase().includes(q) || i.hari.toLowerCase().includes(q)
                );
            }
            const dayOrder = { Senin: 1, Selasa: 2, Rabu: 3, Kamis: 4, Jumat: 5, Sabtu: 6, Minggu: 7 };
            if (this.sortColumn) {
                filtered.sort((a, b) => {
                    let vA, vB;
                    if (this.sortColumn === 'hari') {
                        vA = dayOrder[a.hari] || 99;
                        vB = dayOrder[b.hari] || 99;
                    } else {
                        vA = (a[this.sortColumn] || '').toString().toLowerCase();
                        vB = (b[this.sortColumn] || '').toString().toLowerCase();
                    }
                    if (vA < vB) return this.sortDirection === 'asc' ? -1 : 1;
                    if (vA > vB) return this.sortDirection === 'asc' ? 1 : -1;
                    return 0;
                });
            }
            return filtered;
        },
        get totalPages() { return Math.ceil(this.filteredItems.length / this.itemsPerPage) || 1; },
        get paginatedItems() {
            if (this.currentPage > this.totalPages) this.currentPage = 1;
            const s = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredItems.slice(s, s + this.itemsPerPage);
        }
    }
}
</script>
@endsection
