@extends('layouts.guru')

@section('title', 'Absensi')

@section('content')
<div class="container-fluid" x-data="absensiManager()">

    {{-- Filter Periode --}}
    <div class="paud-card mb-4">
        <div class="card-body py-3 px-4">
            <form method="GET" action="{{ route('guru.absensi.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
                <span style="font-size:0.85rem; font-weight:600; color: var(--paud-muted);">
                    <i class="bi bi-funnel me-1"></i> Filter:
                </span>
                <select name="periode" class="form-select form-select-sm" style="width:120px; border-radius:20px; border-color: var(--paud-border); font-size:0.85rem;" onchange="this.form.submit()">
                    <option value="Ganjil" {{ $periode == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ $periode == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
                <select name="tahun_ajaran" class="form-select form-select-sm" style="width:140px; border-radius:20px; border-color: var(--paud-border); font-size:0.85rem;" onchange="this.form.submit()">
                    @for($y = 2026; $y <= (int)date('Y') + 5; $y++)
                        <option value="{{ $y }}/{{ $y+1 }}" {{ $tahunAjaran == "$y/".($y+1) ? 'selected' : '' }}>
                            {{ $y }}/{{ $y+1 }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    {{-- Header + Search --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1" style="color: var(--paud-text);">
                <span style="border-left: 3px solid var(--paud-teal); padding-left: 12px;">Jadwal Mengajar Anda</span>
            </h5>
            <p style="font-size:0.85rem; color: var(--paud-muted); margin-left: 15px;" class="mb-0">
                <span x-text="filteredItems.length"></span> jadwal ditemukan
            </p>
        </div>
        <div class="input-group" style="width: 260px;">
            <span class="input-group-text border-0" style="background: var(--paud-card); border-radius: var(--paud-radius-sm) 0 0 var(--paud-radius-sm);">
                <i class="bi bi-search" style="color: var(--paud-muted);"></i>
            </span>
            <input type="search" class="form-control border-0 shadow-none" placeholder="Cari kelas atau hari..."
                x-model.debounce.300ms="searchQuery"
                style="background: var(--paud-card); border-radius: 0 var(--paud-radius-sm) var(--paud-radius-sm) 0; font-size:0.88rem;">
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius: var(--paud-radius-sm); border: none;" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Card Grid --}}
    <div class="row g-3">
        <template x-for="(item, index) in paginatedItems" :key="item.id">
            <div class="col-md-6 col-lg-4">
                <div class="paud-card p-0 h-100 jadwal-card">
                    <div class="p-4">
                        {{-- Day Badge --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="paud-badge day-badge" :class="getDayColor(item.hari)" x-text="item.hari"></span>
                            <span style="font-size:0.78rem; color: var(--paud-muted);" x-text="'#' + ((currentPage - 1) * itemsPerPage + index + 1)"></span>
                        </div>

                        {{-- Class Name --}}
                        <h6 class="fw-bold mb-2" style="color: var(--paud-text);" x-text="item.kelas_nama"></h6>

                        {{-- Time --}}
                        <div class="d-flex align-items-center mb-3" style="color: var(--paud-muted); font-size:0.88rem;">
                            <i class="bi bi-clock me-2"></i>
                            <span x-text="item.jam"></span>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2 mt-auto">
                            <a :href="item.kalender_url" class="btn paud-btn-primary btn-sm flex-fill">
                                <i class="bi bi-calendar2-week me-1"></i> Kalender
                            </a>
                            <a :href="item.rekap_url" class="btn paud-btn-outline btn-sm flex-fill">
                                <i class="bi bi-clipboard-data me-1"></i> Rekap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Empty State --}}
        <div class="col-12" x-show="filteredItems.length === 0">
            <div class="paud-card text-center py-5">
                <i class="bi bi-calendar-x" style="font-size:3rem; color: var(--paud-border);"></i>
                <p class="mt-3 mb-0" style="color: var(--paud-muted);">Tidak ada jadwal ditemukan.</p>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <nav x-show="totalPages > 1" class="d-flex justify-content-end mt-4">
        <ul class="pagination" style="gap:4px;">
            <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                <a class="page-link paud-page-link" href="#" @click.prevent="currentPage--">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            <template x-for="page in totalPages" :key="page">
                <li class="page-item" :class="{ 'active': currentPage === page }">
                    <a class="page-link paud-page-link" href="#" @click.prevent="currentPage = page" x-text="page"></a>
                </li>
            </template>
            <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                <a class="page-link paud-page-link" href="#" @click.prevent="currentPage++">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
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

<style>
    .jadwal-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .jadwal-card:hover {
        transform: translateY(-3px);
    }

    /* Day badge colors — PAUD Bougenville palette */
    .day-senin    { background: #EFF6FF; color: #1D4ED8; }   /* Blue */
    .day-selasa   { background: #F0FDF4; color: #15803D; }   /* Green */
    .day-rabu     { background: #FFF7ED; color: #C2410C; }   /* Orange-700 */
    .day-kamis    { background: #FFF7E6; color: #B45309; }   /* Amber */
    .day-jumat    { background: #FDF4FF; color: #7E22CE; }   /* Purple */
    .day-sabtu    { background: #FFF1F2; color: #BE123C; }   /* Rose */
    .day-minggu   { background: #F8FAFC; color: #475569; }   /* Slate */

    /* Pagination */
    .paud-page-link {
        border: none !important;
        border-radius: 8px !important;
        color: var(--paud-muted) !important;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 6px 12px !important;
    }
    .page-item.active .paud-page-link {
        background: var(--paud-teal) !important;
        color: #fff !important;
    }
    .paud-page-link:hover {
        background: var(--paud-teal-light) !important;
        color: var(--paud-teal) !important;
    }
</style>

<script>
function absensiManager() {
    return {
        searchQuery: '',
        sortColumn: 'hari',
        sortDirection: 'asc',
        currentPage: 1,
        itemsPerPage: 9,

        items: @json($jadwalData),

        getDayColor(hari) {
            const map = {
                'Senin': 'day-senin',
                'Selasa': 'day-selasa',
                'Rabu': 'day-rabu',
                'Kamis': 'day-kamis',
                'Jumat': 'day-jumat',
                'Sabtu': 'day-sabtu',
                'Minggu': 'day-minggu',
            };
            return map[hari] || 'day-senin';
        },

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
