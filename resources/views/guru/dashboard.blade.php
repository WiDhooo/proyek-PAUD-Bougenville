@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid">

    <!-- Greeting -->
    <div class="mb-4">
        <h3 class="fw-bold" style="color: var(--paud-text);">
            Halo, {{ Auth::user()->name ?? 'Guru' }}!
        </h3>
        <p style="color: var(--paud-muted);">Berikut jadwal mengajar pekan ini — {{ now()->translatedFormat('l, d F Y')
            }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="paud-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-paud-teal-light text-paud-teal me-3">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <div>
                        <div style="font-size:0.82rem; color: var(--paud-muted); font-weight:500;">Total Jadwal</div>
                        <div class="fw-bold fs-4" style="color: var(--paud-text);">{{ $jadwal->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="paud-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-paud-amber-light text-paud-amber me-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div style="font-size:0.82rem; color: var(--paud-muted); font-weight:500;">Jumlah Kelas</div>
                        <div class="fw-bold fs-4" style="color: var(--paud-text);">
                            {{ $jadwal->pluck('kelas.nama_kelas')->unique()->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="paud-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-paud-coral-light text-paud-coral me-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <div style="font-size:0.82rem; color: var(--paud-muted); font-weight:500;">Total Pertemuan</div>
                        <div class="fw-bold fs-4" style="color: var(--paud-text);">{{ $jadwal->count() }} Kali</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="paud-card">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-paud-teal">
                    <i class="bi bi-calendar-week me-2"></i>Jadwal Mengajar Pekan Ini
                </h5>
                <span style="font-size:0.82rem; color: var(--paud-muted);">
                    Update terakhir: {{ now()->translatedFormat('d F Y') }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="paud-thead" style="border-radius: var(--paud-radius-sm);">
                        <tr>
                            <th class="py-3 px-3" style="border-radius: var(--paud-radius-sm) 0 0 0;">Hari</th>
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3" style="border-radius: 0 var(--paud-radius-sm) 0 0;">Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($jadwal as $item)
                        <tr class="paud-table-row">
                            <td class="fw-semibold px-3 py-3">{{ $item->hari }}</td>
                            <td class="px-3 py-3">
                                <span class="paud-badge bg-paud-teal-light text-paud-teal">
                                    {{ $item->kelas->nama_kelas ?? '-' }} — {{ $item->kelas->kelas ?? '' }}
                                </span>
                            </td>
                            <td class="px-3 py-3" style="color: var(--paud-muted);">
                                @if($item->waktu_mulai && $item->waktu_selesai)
                                <i class="bi bi-clock me-1"></i>
                                {{ $item->waktu_mulai->format('H:i') }} - {{ $item->waktu_selesai->format('H:i') }}
                                @else
                                <span style="color: var(--paud-muted);">Belum diatur</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5" style="color: var(--paud-muted);">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada jadwal tersedia.
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