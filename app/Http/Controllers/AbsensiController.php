<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    private const DEFAULT_PERIODE     = 'Ganjil';
    private const DEFAULT_TAHUN_AJARAN = '2026/2027';
    private const MAX_SISWA_PER_SUBMIT = 100; // [REL-1]

    // Semester bounds: Ganjil = Jul–Des, Genap = Jan–Jun
    private const SEMESTER_BOUNDS = [
        'Ganjil' => ['start_month' => 7,  'end_month' => 12],
        'Genap'  => ['start_month' => 1,  'end_month' => 6],
    ];

    // [MAIN-3] Konstanta day map — tidak ada magic array inline
    private const DAY_MAP = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
        4 => 'Kamis', 5 => 'Jumat',  6 => 'Sabtu', 7 => 'Minggu',
    ];

    // ================================================================
    // PUBLIC METHODS
    // ================================================================

    /**
     * Daftar jadwal guru — style tabel Data Siswa.
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);

        $jadwals = Jadwal::where('guru_id', $guru->id)
            ->with('kelas')
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat')")
            ->get();

        return view('guru.absensi.index', compact('jadwals', 'guru', 'periode', 'tahunAjaran'));
    }

    /**
     * Kalender bulan: hari jadwal = hijau, hari lain = disabled.
     */
    public function kalender($jadwalId, Request $request)
    {
        $jadwal = Jadwal::with('kelas')->findOrFail($jadwalId);
        $this->authorizeJadwal($jadwal);

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);
        $bulan       = (int) $request->query('bulan', now()->month);
        $tahun       = (int) $request->query('tahun', now()->year);

        $ctx = $this->getSemesterContext($periode, $tahunAjaran, $bulan, $tahun);
        [$bulan, $tahun] = [$ctx['bulan'], $ctx['tahun']];

        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate   = $startDate->copy()->endOfMonth();

        // Absensi yang sudah diisi (indexed query on jadwal_id + tanggal)
        $absensiDates = Absensi::where('jadwal_id', $jadwalId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('tanggal, COUNT(DISTINCT siswa_id) as jumlah_siswa')
            ->groupBy('tanggal')
            ->pluck('jumlah_siswa', 'tanggal')
            ->toArray();

        $totalSiswa = Siswa::where('kelas_id', $jadwal->kelas_id)->count();

        // Generate calendar
        $calendarDays = [];
        $current      = $startDate->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd  = $endDate->copy()->endOfWeek(Carbon::SUNDAY);

        while ($current <= $calendarEnd) {
            $dateStr        = $current->toDateString();
            $isCurrentMonth = $current->month == $bulan;
            $isJadwalDay    = $this->dayMatch($current->dayOfWeekIso, $jadwal->hari);
            $sudahAbsen     = isset($absensiDates[$dateStr]);

            $calendarDays[] = [
                'tanggal'          => $dateStr,
                'day'              => $current->day,
                'is_current_month' => $isCurrentMonth,
                'is_jadwal_day'    => $isJadwalDay,
                'is_today'         => $current->isToday(),
                'sudah_absen'      => $sudahAbsen,
                'jumlah_absen'     => $absensiDates[$dateStr] ?? 0,
                'can_absen'        => $isJadwalDay && $isCurrentMonth,
            ];

            $current->addDay();
        }

        $namaBulan = Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y');

        return view('guru.absensi.kalender', compact(
            'jadwal', 'calendarDays', 'bulan', 'tahun', 'namaBulan', 'totalSiswa',
            'periode', 'tahunAjaran'
        ) + $ctx);
    }

    /**
     * Form input absensi semua siswa.
     */
    public function inputAbsensi($jadwalId, $tanggal, Request $request)
    {
        // [SEC-1] Validasi format tanggal dari route param sebelum diproses
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            abort(400, 'Format tanggal tidak valid.');
        }

        $jadwal = Jadwal::with('kelas')->findOrFail($jadwalId);
        $this->authorizeJadwal($jadwal);

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);

        // Validasi: tanggal harus hari jadwal
        $tanggalCarbon = Carbon::parse($tanggal);
        if (!$this->dayMatch($tanggalCarbon->dayOfWeekIso, $jadwal->hari)) {
            return redirect()->route('guru.absensi.kalender', [
                'id' => $jadwalId, 'periode' => $periode, 'tahun_ajaran' => $tahunAjaran,
            ])->with('error', 'Tidak bisa absen di hari ini karena bukan hari jadwal.');
        }

        $siswaList = Siswa::where('kelas_id', $jadwal->kelas_id)->orderBy('nama')->get();

        $existing = Absensi::where('jadwal_id', $jadwalId)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        $tanggalFormatted = $tanggalCarbon->locale('id')->translatedFormat('l, d F Y');

        return view('guru.absensi.input', compact(
            'jadwal', 'siswaList', 'tanggal', 'tanggalFormatted',
            'existing', 'periode', 'tahunAjaran'
        ));
    }

    /**
     * Simpan absensi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id'    => 'required|exists:jadwal,id',
            'tanggal'      => 'required|date_format:Y-m-d', // [SEC-1] ketat format tanggal
            'status'       => 'required|array',
            'status.*'     => 'required|in:H,S,I,A',
            'periode'      => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
        ]);

        $jadwalId = $request->jadwal_id;
        $tanggal  = $request->tanggal;

        // Bonus: store() juga harus memverifikasi kepemilikan jadwal
        $jadwal = Jadwal::findOrFail($jadwalId);
        $this->authorizeJadwal($jadwal);

        // [SEC-2] Hanya proses siswa_id yang benar-benar ada di kelas ini
        $validSiswaIds = Siswa::where('kelas_id', $jadwal->kelas_id)
            ->pluck('id')
            ->flip()
            ->toArray();
        $statusInput = array_intersect_key($request->status, $validSiswaIds);

        abort_if(empty($statusInput), 422, 'Tidak ada siswa valid untuk diabsen.');

        // [REL-1] Batas maksimum siswa per submit — cegah abuse
        abort_if(
            count($statusInput) > self::MAX_SISWA_PER_SUBMIT,
            422,
            'Jumlah siswa melebihi batas maksimum (' . self::MAX_SISWA_PER_SUBMIT . ').'
        );

        DB::beginTransaction();
        try {
            foreach ($statusInput as $siswaId => $status) {
                Absensi::updateOrCreate(
                    ['jadwal_id' => $jadwalId, 'siswa_id' => $siswaId, 'tanggal' => $tanggal],
                    [
                        'status'       => $status,
                        'keterangan'   => $request->keterangan[$siswaId] ?? null,
                        'periode'      => $request->periode,
                        'tahun_ajaran' => $request->tahun_ajaran,
                    ]
                );
            }
            DB::commit();

            // [OBS-1] Log sukses dengan konteks
            Log::info('Absensi disimpan', [
                'user_id'      => Auth::id(),
                'jadwal_id'    => $jadwalId,
                'tanggal'      => $tanggal,
                'periode'      => $request->periode,
                'jumlah_siswa' => count($statusInput),
            ]);

            $date = Carbon::parse($tanggal);
            return redirect()->route('guru.absensi.kalender', [
                'id'           => $jadwalId,
                'bulan'        => $date->month,
                'tahun'        => $date->year,
                'periode'      => $request->periode,
                'tahun_ajaran' => $request->tahun_ajaran,
            ])->with('success', 'Absensi tanggal ' . $date->locale('id')->translatedFormat('d F Y') . ' berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();

            // [OBS-1] Log error dengan konteks lengkap untuk debugging production
            Log::error('Gagal menyimpan absensi', [
                'user_id'   => Auth::id(),
                'jadwal_id' => $jadwalId,
                'tanggal'   => $tanggal,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'file'      => $e->getFile() . ':' . $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Gagal menyimpan absensi. Silakan coba lagi.');
        }
    }

    /**
     * Rekap bulanan kehadiran per semester.
     */
    public function rekap($jadwalId, Request $request)
    {
        $jadwal = Jadwal::with('kelas')->findOrFail($jadwalId);
        $this->authorizeJadwal($jadwal);

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);
        $bulan       = (int) $request->query('bulan', now()->month);
        $tahun       = (int) $request->query('tahun', now()->year);

        $ctx = $this->getSemesterContext($periode, $tahunAjaran, $bulan, $tahun);
        [$bulan, $tahun] = [$ctx['bulan'], $ctx['tahun']];

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // [MAIN-1] Gunakan shared helper — tidak duplikat kode
        $rekap = $this->buildMonthlyRekap($jadwal, $startDate, $endDate);

        $namaBulan = Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y');

        return view('guru.absensi.rekap', compact('jadwal', 'rekap', 'bulan', 'tahun', 'namaBulan', 'periode', 'tahunAjaran')
            + $ctx);
    }

    /**
     * Rekap keseluruhan satu semester — semua jadwal/hari untuk satu kelas.
     */
    public function rekapSemester($kelasId, Request $request)
    {
        [$kelas, $jadwals, $hariList, $rekap, $namaBulanList, $judulSemester, $periode, $tahunAjaran]
            = $this->buildSemesterRekap($kelasId, $request);

        return view('guru.absensi.rekap_semester', compact(
            'kelas', 'jadwals', 'hariList', 'rekap', 'namaBulanList', 'judulSemester', 'periode', 'tahunAjaran'
        ));
    }

    /**
     * Export rekap bulanan ke PDF.
     */
    public function exportPdf($jadwalId, Request $request)
    {
        $jadwal = Jadwal::with('kelas')->findOrFail($jadwalId);
        $this->authorizeJadwal($jadwal);

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);
        $bulan       = (int) $request->query('bulan', now()->month);
        $tahun       = (int) $request->query('tahun', now()->year);

        $ctx = $this->getSemesterContext($periode, $tahunAjaran, $bulan, $tahun);
        [$bulan, $tahun] = [$ctx['bulan'], $ctx['tahun']];

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();
        $namaBulan = Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y');

        // [MAIN-1] Gunakan shared helper
        $rekap = $this->buildMonthlyRekap($jadwal, $startDate, $endDate);

        // [SEC-3] Sanitasi nama file — cegah path traversal
        $filename = $this->sanitizeFilename(
            'Rekap-Absensi-' . $jadwal->kelas->nama_kelas . '-' . $namaBulan
        ) . '.pdf';

        // [OBS-2] Log aktivitas export
        Log::info('Export PDF rekap bulanan', [
            'user_id'      => Auth::id(),
            'jadwal_id'    => $jadwal->id,
            'bulan'        => "{$tahun}-{$bulan}",
            'tahun_ajaran' => $tahunAjaran,
            'filename'     => $filename,
        ]);

        // [REL-2] Bungkus PDF render dalam try-catch
        try {
            $pdf = Pdf::loadView('guru.absensi.export_pdf', compact(
                'jadwal', 'rekap', 'namaBulan', 'periode', 'tahunAjaran'
            ))->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF rekap bulanan', [
                'user_id'  => Auth::id(),
                'jadwal_id'=> $jadwal->id,
                'exception'=> get_class($e),
                'message'  => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Gagal membuat PDF. Silakan coba lagi.');
        }
    }

    /**
     * Export rekap bulanan ke Excel/CSV.
     */
    public function exportExcel($jadwalId, Request $request)
    {
        $jadwal = Jadwal::with('kelas')->findOrFail($jadwalId);
        $this->authorizeJadwal($jadwal);

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);
        $bulan       = (int) $request->query('bulan', now()->month);
        $tahun       = (int) $request->query('tahun', now()->year);

        $ctx = $this->getSemesterContext($periode, $tahunAjaran, $bulan, $tahun);
        [$bulan, $tahun] = [$ctx['bulan'], $ctx['tahun']];

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();
        $namaBulan = Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y');

        // [MAIN-1] Gunakan shared helper
        $rekap = $this->buildMonthlyRekap($jadwal, $startDate, $endDate);

        $rows   = [];
        $rows[] = ['Rekap Absensi - ' . $jadwal->kelas->nama_kelas . ' - ' . $namaBulan];
        $rows[] = ['Periode: ' . $periode . ' ' . $tahunAjaran];
        $rows[] = [];
        $rows[] = ['No', 'Nama Siswa', 'Hadir (H)', 'Sakit (S)', 'Izin (I)', 'Alpha (A)', 'Total', '% Kehadiran'];

        foreach ($rekap as $i => $r) {
            $rows[] = [
                $i + 1,
                $r['siswa']->nama,
                $r['hadir'],
                $r['sakit'],
                $r['izin'],
                $r['alpha'],
                $r['total'],
                $r['persen'] . '%',
            ];
        }

        // [SEC-3] Sanitasi nama file
        $filename = $this->sanitizeFilename(
            'Rekap-Absensi-' . $jadwal->kelas->nama_kelas . '-' . $namaBulan
        ) . '.csv';

        // [OBS-2] Log aktivitas export
        Log::info('Export Excel rekap bulanan', [
            'user_id'      => Auth::id(),
            'jadwal_id'    => $jadwal->id,
            'bulan'        => "{$tahun}-{$bulan}",
            'tahun_ajaran' => $tahunAjaran,
        ]);

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8 for Excel
            foreach ($rows as $row) {
                fputcsv($handle, $row, ';'); // semicolon untuk locale Excel Indonesia
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export rekap semester ke PDF.
     */
    public function exportSemesterPdf($kelasId, Request $request)
    {
        [$kelas, $jadwals, $hariList, $rekap, $namaBulanList, $judulSemester, $periode, $tahunAjaran]
            = $this->buildSemesterRekap($kelasId, $request);

        // [SEC-3] Sanitasi nama file
        $filename = $this->sanitizeFilename(
            'Rekap-Semester-' . $kelas->nama_kelas . '-' . $judulSemester
        ) . '.pdf';

        // [OBS-2] Log export
        Log::info('Export PDF rekap semester', [
            'user_id'      => Auth::id(),
            'kelas_id'     => $kelas->id,
            'semester'     => $judulSemester,
        ]);

        // [REL-2] try-catch untuk PDF render
        try {
            $pdf = Pdf::loadView('guru.absensi.export_semester_pdf', compact(
                'kelas', 'jadwals', 'hariList', 'rekap', 'namaBulanList', 'judulSemester', 'periode', 'tahunAjaran'
            ))->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF rekap semester', [
                'user_id'  => Auth::id(),
                'kelas_id' => $kelas->id,
                'exception'=> get_class($e),
                'message'  => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Gagal membuat PDF. Silakan coba lagi.');
        }
    }

    /**
     * Export rekap semester ke Excel/CSV.
     */
    public function exportSemesterExcel($kelasId, Request $request)
    {
        [$kelas, $jadwals, $hariList, $rekap, $namaBulanList, $judulSemester, $periode, $tahunAjaran]
            = $this->buildSemesterRekap($kelasId, $request);

        $rows   = [];
        $rows[] = ['Rekap Semester Absensi - ' . $kelas->nama_kelas];
        $rows[] = ['Semester: ' . $judulSemester];
        $rows[] = ['Hari Jadwal: ' . $hariList];
        $rows[] = [];

        $header = ['No', 'Nama Siswa'];
        foreach ($namaBulanList as $nb) {
            $header[] = $nb . ' (H)';
            $header[] = $nb . ' (S)';
            $header[] = $nb . ' (I)';
            $header[] = $nb . ' (A)';
        }
        $header[] = 'Total Hadir';
        $header[] = 'Total Pertemuan';
        $header[] = '% Kehadiran';
        $rows[]   = $header;

        foreach ($rekap as $i => $r) {
            $row = [$i + 1, $r['siswa']->nama];
            foreach ($namaBulanList as $nb) {
                $bln   = $r['per_bulan'][$nb] ?? ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                $row[] = $bln['H'];
                $row[] = $bln['S'];
                $row[] = $bln['I'];
                $row[] = $bln['A'];
            }
            $row[]  = $r['hadir'];
            $row[]  = $r['total'];
            $row[]  = $r['persen'] . '%';
            $rows[] = $row;
        }

        // [SEC-3] Sanitasi nama file
        $filename = $this->sanitizeFilename(
            'Rekap-Semester-' . $kelas->nama_kelas . '-' . $judulSemester
        ) . '.csv';

        // [OBS-2] Log export
        Log::info('Export Excel rekap semester', [
            'user_id'  => Auth::id(),
            'kelas_id' => $kelas->id,
            'semester' => $judulSemester,
        ]);

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ================================================================
    // PRIVATE HELPERS
    // ================================================================

    /**
     * [OBS-3] Authorization guard — cegah guru akses jadwal guru lain.
     * Log setiap percobaan akses tidak sah untuk audit trail.
     */
    private function authorizeJadwal(Jadwal $jadwal): void
    {
        $guru = Auth::user()->guru;
        if (!$guru || $jadwal->guru_id !== $guru->id) {
            // [OBS-3] Catat percobaan akses tidak sah
            Log::warning('Unauthorized jadwal access attempt', [
                'user_id'     => Auth::id(),
                'jadwal_id'   => $jadwal->id,
                'owner_id'    => $jadwal->guru_id,
                'ip'          => request()->ip(),
            ]);
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }
    }

    /**
     * [MAIN-1] Shared helper — build rekap bulanan.
     * Dipakai oleh rekap(), exportPdf(), exportExcel() — tidak duplikasi.
     */
    private function buildMonthlyRekap(Jadwal $jadwal, Carbon $startDate, Carbon $endDate): array
    {
        $siswaList = Siswa::where('kelas_id', $jadwal->kelas_id)->orderBy('nama')->get();
        $absensis  = Absensi::where('jadwal_id', $jadwal->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        // Pre-group by siswa_id untuk menghindari linear scan berulang
        $grouped = $absensis->groupBy('siswa_id');

        $rekap = [];
        foreach ($siswaList as $siswa) {
            $sa    = $grouped->get($siswa->id, collect());
            $total = $sa->count();
            $hadir = $sa->where('status', 'H')->count();

            $rekap[] = [
                'siswa'  => $siswa,
                'hadir'  => $hadir,
                'sakit'  => $sa->where('status', 'S')->count(),
                'izin'   => $sa->where('status', 'I')->count(),
                'alpha'  => $sa->where('status', 'A')->count(),
                'total'  => $total,
                'persen' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
            ];
        }

        return $rekap;
    }

    /**
     * Shared helper — build semester rekap (dipakai oleh view, PDF, Excel).
     */
    private function buildSemesterRekap($kelasId, Request $request): array
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            // [OBS-4] Log jika user tidak punya data guru
            Log::warning('Semester rekap access without guru data', ['user_id' => Auth::id()]);
            abort(403, 'Data guru tidak ditemukan.');
        }

        $jadwals = Jadwal::where('guru_id', $guru->id)
            ->where('kelas_id', $kelasId)
            ->with('kelas')
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat')")
            ->get();

        if ($jadwals->isEmpty()) {
            // [OBS-4] Log akses tidak sah ke kelas orang lain
            Log::warning('Unauthorized semester rekap access', [
                'user_id'   => Auth::id(),
                'kelas_id'  => $kelasId,
                'ip'        => request()->ip(),
            ]);
            abort(403, 'Anda tidak memiliki jadwal di kelas ini.');
        }

        $kelas     = $jadwals->first()->kelas;
        $jadwalIds = $jadwals->pluck('id');
        $hariList  = $jadwals->pluck('hari')->unique()->implode(', ');

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);
        $bounds      = self::SEMESTER_BOUNDS[$periode];
        $tahunStart  = $this->getTahunStart($periode, $tahunAjaran);
        $startDate   = Carbon::create($tahunStart, $bounds['start_month'], 1)->startOfMonth();
        $endDate     = Carbon::create($tahunStart, $bounds['end_month'],   1)->endOfMonth();

        $absensis  = Absensi::whereIn('jadwal_id', $jadwalIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
        $siswaList = Siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();

        // [PERF-2] Pre-group SATU KALI sebelum loop ganda siswa × bulan
        // Struktur: ['siswa_id' => ['Y-m' => Collection<Absensi>]]
        $absensiGrouped = $absensis
            ->groupBy('siswa_id')
            ->map(fn($rows) => $rows->groupBy(fn($a) => substr($a->tanggal, 0, 7)));

        // Pre-build daftar bulan sekali (bukan di dalam loop siswa)
        $bulanKeys = [];
        for ($m = $bounds['start_month']; $m <= $bounds['end_month']; $m++) {
            $key          = Carbon::create($tahunStart, $m, 1)->format('Y-m');
            $namaBln      = Carbon::create($tahunStart, $m, 1)->locale('id')->translatedFormat('M');
            $bulanKeys[]  = ['key' => $key, 'label' => $namaBln];
        }

        $rekap = [];
        foreach ($siswaList as $siswa) {
            // [PERF-2] O(1) lookup — tidak ada linear scan
            $saBySiswa = $absensiGrouped->get($siswa->id, collect());
            $allRows   = $saBySiswa->flatten();
            $total     = $allRows->count();
            $hadir     = $allRows->where('status', 'H')->count();

            $perBulan = [];
            foreach ($bulanKeys as $bk) {
                $saBulan         = $saBySiswa->get($bk['key'], collect());
                $perBulan[$bk['label']] = [
                    'H' => $saBulan->where('status', 'H')->count(),
                    'S' => $saBulan->where('status', 'S')->count(),
                    'I' => $saBulan->where('status', 'I')->count(),
                    'A' => $saBulan->where('status', 'A')->count(),
                ];
            }

            $rekap[] = [
                'siswa'     => $siswa,
                'hadir'     => $hadir,
                'sakit'     => $allRows->where('status', 'S')->count(),
                'izin'      => $allRows->where('status', 'I')->count(),
                'alpha'     => $allRows->where('status', 'A')->count(),
                'total'     => $total,
                'persen'    => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
                'per_bulan' => $perBulan,
            ];
        }

        $namaBulanList = collect($bulanKeys)->pluck('label')->toArray();

        $judulSemester = "{$periode} {$tahunAjaran} (" .
            Carbon::create($tahunStart, $bounds['start_month'], 1)->locale('id')->translatedFormat('M') . '–' .
            Carbon::create($tahunStart, $bounds['end_month'],   1)->locale('id')->translatedFormat('M Y') . ')';

        return [$kelas, $jadwals, $hariList, $rekap, $namaBulanList, $judulSemester, $periode, $tahunAjaran];
    }

    /**
     * DRY — Semester context: clamp bulan/tahun + hitung prev/next bounds.
     */
    private function getSemesterContext(string $periode, string $tahunAjaran, int $bulan, int $tahun): array
    {
        $bounds     = self::SEMESTER_BOUNDS[$periode];
        $tahunStart = $this->getTahunStart($periode, $tahunAjaran);
        $startBound = Carbon::create($tahunStart, $bounds['start_month'], 1);
        $endBound   = Carbon::create($tahunStart, $bounds['end_month'], 1)->endOfMonth();
        $requested  = Carbon::create($tahun, $bulan, 1);

        // Clamp ke rentang semester
        if ($requested->lt($startBound)) {
            $bulan = $bounds['start_month'];
            $tahun = $tahunStart;
        } elseif ($requested->gt($endBound)) {
            $bulan = $bounds['end_month'];
            $tahun = $tahunStart;
        }

        $prevMonth = $bulan == 1  ? 12 : $bulan - 1;
        $prevYear  = $bulan == 1  ? $tahun - 1 : $tahun;
        $nextMonth = $bulan == 12 ? 1  : $bulan + 1;
        $nextYear  = $bulan == 12 ? $tahun + 1 : $tahun;

        return [
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'startBound'  => $startBound,
            'endBound'    => $endBound,
            'prevMonth'   => $prevMonth,
            'prevYear'    => $prevYear,
            'nextMonth'   => $nextMonth,
            'nextYear'    => $nextYear,
            'prevAllowed' => Carbon::create($prevYear, $prevMonth, 1)->gte($startBound),
            'nextAllowed' => Carbon::create($nextYear, $nextMonth, 1)->lte($endBound),
        ];
    }

    /**
     * [MAIN-3] Match hari — gunakan konstanta DAY_MAP, bukan magic array inline.
     */
    private function dayMatch(int $dayOfWeekIso, string $hari): bool
    {
        return (self::DAY_MAP[$dayOfWeekIso] ?? '') === $hari;
    }

    /**
     * Tahun awal semester: Ganjil = tahun pertama, Genap = tahun kedua.
     * [SEC-4][REL-4] Validasi format sebelum explode.
     */
    private function getTahunStart(string $periode, string $tahunAjaran): int
    {
        // [SEC-4] Guard — cegah undefined index jika format salah
        if (!preg_match('/^\d{4}\/\d{4}$/', $tahunAjaran)) {
            Log::error('Format tahun_ajaran tidak valid', [
                'user_id'      => Auth::id(),
                'tahun_ajaran' => $tahunAjaran,
            ]);
            throw new \InvalidArgumentException("Format tahun ajaran tidak valid: {$tahunAjaran}");
        }

        $parts = explode('/', $tahunAjaran);
        return $periode === 'Ganjil' ? (int) $parts[0] : (int) $parts[1];
    }

    /**
     * [SEC-3] Sanitasi nama file export — cegah path traversal dan karakter tidak aman.
     */
    private function sanitizeFilename(string $name): string
    {
        return preg_replace('/[^A-Za-z0-9\-\_\. ]/', '_', $name);
    }
}
