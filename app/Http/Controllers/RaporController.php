<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\AspekPenilaian;
use App\Models\NilaiRapor;
use App\Models\HasilAnalisis;
use App\Services\MlRaporService;
use App\Services\SmartAnalysisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RaporController extends Controller
{
    // [M2] Constants — menghilangkan magic strings
    private const DEFAULT_PERIODE = 'Ganjil';
    private const DEFAULT_TAHUN_AJARAN = '2026/2027';

    protected MlRaporService $mlService;
    protected SmartAnalysisService $smartAnalysisService;

    public function __construct(MlRaporService $mlService, SmartAnalysisService $smartAnalysisService)
    {
        $this->mlService = $mlService;
        $this->smartAnalysisService = $smartAnalysisService;
    }

    // ================================================================
    // NAVIGATION PAGES
    // ================================================================

    public function pilihKelas()
    {
        $guru = Auth::user()->guru;

        // [P-5][S-1] Hanya tampilkan kelas yang ada di jadwal guru yg login
        $kelasIds = $guru
            ? Jadwal::where('guru_id', $guru->id)->pluck('kelas_id')->unique()
            : collect();

        $kelasList = Kelas::withCount('siswa')
            ->whereIn('id', $kelasIds)
            ->get();

        return view('guru.rapor.pilih_kelas', compact('kelasList'));
    }

    public function daftarSiswa($kelasId, Request $request)
    {
        $kelas = Kelas::findOrFail($kelasId);

        // [S-1] Guard: pastikan kelas ini memang milik guru yang login
        $guru = Auth::user()->guru;
        if ($guru) {
            $kelasGuruIds = Jadwal::where('guru_id', $guru->id)->pluck('kelas_id');
            if (!$kelasGuruIds->contains($kelasId)) {
                Log::warning('Unauthorized kelas access attempt', [
                    'user_id'  => Auth::id(),
                    'kelas_id' => $kelasId,
                    'ip'       => request()->ip(),
                ]);
                abort(403, 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        $periode     = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);

        $siswas = Siswa::where('kelas_id', $kelasId)
            ->with(['hasilAnalises' => function ($q) use ($periode, $tahunAjaran) {
                $q->where('periode', $periode)
                  ->where('tahun_ajaran', $tahunAjaran)
                  ->latest()->limit(1);
            }])
            ->get();

        return view('guru.rapor.daftar_siswa', compact('kelas', 'siswas', 'periode', 'tahunAjaran'));
    }

    /**
     * Detail rapor siswa: grafik radar + tabel nilai + analisis cerdas.
     */
    public function detailRapor($siswaId, Request $request)
    {
        $siswa = Siswa::with('kelas')->findOrFail($siswaId);
        $this->authorizeSiswa($siswa); // [S-1] IDOR protection

        $periode = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);

        // [P2] DRY: gunakan helper method
        [$nilaiRapors, $nilaiPerLingkup, $hasilAnalisis, $smartAnalysis, $clusterProfile] =
            $this->getRaporData($siswaId, $periode, $tahunAjaran);

        return view('guru.rapor.detail', compact(
            'siswa', 'nilaiRapors', 'nilaiPerLingkup', 'hasilAnalisis',
            'smartAnalysis', 'clusterProfile', 'periode', 'tahunAjaran'
        ));
    }

    // ================================================================
    // CRUD NILAI
    // ================================================================

    /**
     * Halaman input nilai.
     */
    public function input(Request $request)
    {
        $kelasId = $request->query('kelas_id');
        $periode = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);

        $siswas = Siswa::where('kelas_id', $kelasId)->get();
        // [P-1] Cache aspek penilaian — data statis, jarang berubah
        $aspekPenilaians = Cache::remember('aspek_penilaians_grouped', 86400, fn() =>
            AspekPenilaian::all()->groupBy('lingkup')
        );
        // [P-5] Hanya tampilkan kelas yang di-assign ke guru yang login
        $guru = Auth::user()->guru;
        $kelasList = $guru
            ? Kelas::whereIn('id', Jadwal::where('guru_id', $guru->id)->pluck('kelas_id'))->get()
            : Kelas::all();

        // Cek siswa mana yang sudah punya nilai di periode ini
        $siswaYangSudahDinilai = NilaiRapor::where('periode', $periode)
            ->where('tahun_ajaran', $tahunAjaran)
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->distinct('siswa_id')
            ->pluck('siswa_id')
            ->toArray();

        return view('guru.rapor.input', compact(
            'siswas', 'aspekPenilaians', 'kelasId', 'periode', 'tahunAjaran',
            'siswaYangSudahDinilai', 'kelasList'
        ));
    }

    /**
     * Simpan nilai (Create).
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nilai' => 'required|array',
            'nilai.*' => 'required|integer|in:1,2,3,4',
            'periode' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2026/2027).',
        ]);

        // Validasi tahun berurutan
        if (!$this->isValidTahunAjaran($request->tahun_ajaran)) {
            return redirect()->back()->with('error', 'Tahun ajaran harus berurutan (contoh: 2026/2027, bukan 2026/2028).');
        }

        // [S-2] Validasi kepemilikan siswa — cegah IDOR
        $siswa = Siswa::findOrFail($request->siswa_id);
        $this->authorizeSiswa($siswa);

        // [S-3] Validasi bahwa semua aspek_penilaian_id dari form benar-benar ada
        $validAspekIds = AspekPenilaian::pluck('id')->toArray();
        $submittedAspekIds = array_keys($request->nilai);
        $invalidIds = array_diff($submittedAspekIds, $validAspekIds);
        if (!empty($invalidIds)) {
            return redirect()->back()->with('error', 'Aspek penilaian tidak valid: ' . implode(', ', $invalidIds));
        }

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $aspekId => $skor) {
                NilaiRapor::updateOrCreate(
                    [
                        'siswa_id' => $request->siswa_id,
                        'aspek_penilaian_id' => $aspekId,
                        'periode' => $request->periode,
                        'tahun_ajaran' => $request->tahun_ajaran,
                    ],
                    ['nilai' => $skor]
                );
            }
            DB::commit();

            // [O1] Audit log
            Log::info('Nilai disimpan', [
                'siswa_id' => $request->siswa_id,
                'periode' => $request->periode,
                'tahun_ajaran' => $request->tahun_ajaran,
                'jumlah_aspek' => count($request->nilai),
            ]);

            return redirect()->back()->with('success', 'Nilai berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            // [R-2] Log error tanpa full trace — hemat disk
            Log::error('Gagal menyimpan nilai', [
                'siswa_id' => $request->siswa_id,
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan nilai. Silakan coba lagi.');
        }
    }

    /**
     * Halaman edit nilai.
     */
    public function editNilai($siswaId, Request $request)
    {
        $siswa = Siswa::with('kelas')->findOrFail($siswaId);
        $this->authorizeSiswa($siswa); // [S-1] IDOR protection

        $periode = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);

        // [P-1] Cache aspek penilaian
        $aspekPenilaians = Cache::remember('aspek_penilaians_grouped', 86400, fn() =>
            AspekPenilaian::all()->groupBy('lingkup')
        );

        $nilaiExisting = NilaiRapor::where('siswa_id', $siswaId)
            ->where('periode', $periode)
            ->where('tahun_ajaran', $tahunAjaran)
            ->pluck('nilai', 'aspek_penilaian_id')
            ->toArray();

        return view('guru.rapor.edit_nilai', compact(
            'siswa', 'aspekPenilaians', 'nilaiExisting', 'periode', 'tahunAjaran'
        ));
    }

    /**
     * Update nilai (Update).
     */
    public function updateNilai(Request $request, $siswaId)
    {
        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'required|integer|in:1,2,3,4',
            'periode' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
        ], [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2026/2027).',
        ]);

        if (!$this->isValidTahunAjaran($request->tahun_ajaran)) {
            return redirect()->back()->with('error', 'Tahun ajaran harus berurutan (contoh: 2026/2027, bukan 2026/2028).');
        }

        $siswa = Siswa::findOrFail($siswaId);
        $this->authorizeSiswa($siswa); // [S-1] IDOR protection

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $aspekId => $skor) {
                NilaiRapor::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'aspek_penilaian_id' => $aspekId,
                        'periode' => $request->periode,
                        'tahun_ajaran' => $request->tahun_ajaran,
                    ],
                    ['nilai' => $skor]
                );
            }
            DB::commit();

            // [O1] Audit log
            Log::info('Nilai diperbarui', [
                'siswa_id' => $siswa->id,
                'nama' => $siswa->nama,
                'periode' => $request->periode,
                'tahun_ajaran' => $request->tahun_ajaran,
            ]);

            return redirect()->route('guru.rapor.detail', [
                'id' => $siswa->id,
                'periode' => $request->periode,
                'tahun_ajaran' => $request->tahun_ajaran,
            ])->with('success', "Nilai {$siswa->nama} berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();
            // [R-2] Log tanpa full trace
            Log::error('Gagal memperbarui nilai', [
                'siswa_id' => $siswaId,
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui nilai. Silakan coba lagi.');
        }
    }

    /**
     * Hapus SEMUA nilai seorang siswa di periode tertentu (Delete).
     */
    public function destroyNilai(Request $request, $siswaId)
    {
        // [S2] Validasi input sebelum delete
        $request->validate([
            'periode' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
        ]);

        $siswa = Siswa::findOrFail($siswaId);
        $this->authorizeSiswa($siswa); // [S-1] IDOR protection

        $periode = $request->periode;
        $tahunAjaran = $request->tahun_ajaran;

        // [R3] Wrap dalam transaction agar konsisten
        DB::beginTransaction();
        try {
            $deleted = NilaiRapor::where('siswa_id', $siswaId)
                ->where('periode', $periode)
                ->where('tahun_ajaran', $tahunAjaran)
                ->delete();

            HasilAnalisis::where('siswa_id', $siswaId)
                ->where('periode', $periode)
                ->where('tahun_ajaran', $tahunAjaran)
                ->delete();

            DB::commit();

            // [O1] Audit log
            Log::info('Nilai dihapus', [
                'siswa_id' => $siswaId,
                'nama' => $siswa->nama,
                'periode' => $periode,
                'tahun_ajaran' => $tahunAjaran,
                'records_deleted' => $deleted,
            ]);

            return redirect()->back()->with('success', "Nilai {$siswa->nama} untuk periode {$periode} {$tahunAjaran} berhasil dihapus ({$deleted} record).");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus nilai', [
                'siswa_id' => $siswaId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Gagal menghapus nilai. Silakan coba lagi.');
        }
    }

    // ================================================================
    // PDF EXPORT
    // ================================================================

    public function exportPdf($siswaId, Request $request)
    {
        $siswa = Siswa::with('kelas')->findOrFail($siswaId);
        $this->authorizeSiswa($siswa); // [S-1] IDOR protection

        $periode = $request->query('periode', self::DEFAULT_PERIODE);
        $tahunAjaran = $request->query('tahun_ajaran', self::DEFAULT_TAHUN_AJARAN);

        // [P2] DRY: reuse helper
        [$nilaiRapors, $nilaiPerLingkup, $hasilAnalisis, $smartAnalysis, $clusterProfile] =
            $this->getRaporData($siswaId, $periode, $tahunAjaran);

        $safeTA = str_replace('/', '-', $tahunAjaran);
        $safeName = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $siswa->nama);

        // [R-1] Try-catch untuk PDF render — konsistensi dengan AbsensiController
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('guru.rapor.rapor_pdf', compact(
                'siswa', 'nilaiRapors', 'nilaiPerLingkup', 'hasilAnalisis',
                'smartAnalysis', 'clusterProfile', 'periode', 'tahunAjaran'
            ));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("rapor-{$safeName}-{$periode}-{$safeTA}.pdf");
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF rapor', [
                'siswa_id' => $siswaId,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Gagal membuat PDF. Silakan coba lagi.');
        }
    }

    // ================================================================
    // FEATURE ENGINEERING + TRIGGER ANALISIS AI
    // ================================================================

    public function generateAnalisis(Request $request)
    {
        // [S1] Validasi input
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'periode' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
        ]);

        $kelasId = $request->kelas_id;
        $periode = $request->periode;
        $tahunAjaran = $request->tahun_ajaran;

        // [P3] Hanya ambil siswa yang punya nilai (whereHas)
        $siswas = Siswa::where('kelas_id', $kelasId)
            ->whereHas('nilaiRapors', function ($q) use ($periode, $tahunAjaran) {
                $q->where('periode', $periode)
                  ->where('tahun_ajaran', $tahunAjaran);
            })
            ->with(['nilaiRapors' => function ($q) use ($periode, $tahunAjaran) {
                $q->where('periode', $periode)
                  ->where('tahun_ajaran', $tahunAjaran)
                  ->with('aspekPenilaian');
            }])
            ->get();

        // Feature engineering: agregasi per lingkup
        $dataForMl = [];
        foreach ($siswas as $siswa) {
            $nilaiPerLingkup = $siswa->nilaiRapors
                ->groupBy(fn ($nr) => $nr->aspekPenilaian->lingkup ?? 'Lainnya')
                ->map(fn ($group) => round($group->avg('nilai'), 2))
                ->toArray();

            if (!empty($nilaiPerLingkup)) {
                $dataForMl[] = [
                    'siswa_id' => $siswa->id,
                    'nilai' => $nilaiPerLingkup,
                ];
            }
        }

        if (count($dataForMl) < 2) {
            return redirect()->back()->with('error',
                "Minimal 2 siswa harus memiliki nilai lengkap untuk periode '{$periode}' tahun '{$tahunAjaran}'. " .
                "Ditemukan: " . count($dataForMl) . " siswa."
            );
        }

        // [R-4] Health check sebelum kirim data — fail fast jika ML service mati
        if (!$this->mlService->healthCheck()) {
            return redirect()->back()->with('error', 'Layanan AI sedang tidak tersedia. Pastikan server Python berjalan.');
        }

        // Kirim ke Python ML Service
        $response = $this->mlService->analyzeData($dataForMl);

        if (!$response || $response['status'] !== 'success') {
            $message = $response['message'] ?? 'Gagal menghubungi layanan AI.';
            Log::warning('ML analysis gagal', ['response' => $response, 'kelas_id' => $kelasId]);
            return redirect()->back()->with('error', $message);
        }

        // [R2] Validasi struktur response sebelum akses
        if (!isset($response['clusters']) || !is_array($response['clusters'])) {
            Log::error('ML response structure invalid', ['response' => $response]);
            return redirect()->back()->with('error', 'Respons dari layanan AI tidak sesuai format yang diharapkan.');
        }

        // Simpan hasil cluster ke database
        DB::beginTransaction();
        try {
            foreach ($response['clusters'] as $siswaId => $clusterId) {
                HasilAnalisis::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'periode' => $periode,
                        'tahun_ajaran' => $tahunAjaran,
                    ],
                    [
                        'cluster_group' => (string) $clusterId,
                        'raw_response' => $response,
                    ]
                );
            }
            DB::commit();

            // [O1] Audit log
            Log::info('Analisis AI berhasil', [
                'kelas_id' => $kelasId,
                'periode' => $periode,
                'tahun_ajaran' => $tahunAjaran,
                'optimal_k' => $response['optimal_k'],
                'silhouette' => $response['silhouette_score'],
                'jumlah_siswa' => count($response['clusters']),
            ]);

            $msg = "Analisis berhasil! K={$response['optimal_k']}, "
                 . "Silhouette={$response['silhouette_score']}. "
                 . count($response['clusters']) . " siswa dianalisis.";

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan hasil analisis', [
                'kelas_id' => $kelasId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan hasil analisis. Silakan coba lagi.');
        }
    }

    // ================================================================
    // PRIVATE HELPERS
    // ================================================================

    /**
     * [P2] DRY helper: ambil data rapor + smart analysis untuk siswa.
     * Digunakan oleh detailRapor() dan exportPdf().
     *
     * @return array [$nilaiRapors, $nilaiPerLingkup, $hasilAnalisis, $smartAnalysis, $clusterProfile]
     */
    private function getRaporData(int $siswaId, string $periode, string $tahunAjaran): array
    {
        $nilaiRapors = NilaiRapor::where('siswa_id', $siswaId)
            ->where('periode', $periode)
            ->where('tahun_ajaran', $tahunAjaran)
            ->with('aspekPenilaian')
            ->orderBy('aspek_penilaian_id')
            ->get();

        $nilaiPerLingkup = $nilaiRapors->groupBy(function ($item) {
            return $item->aspekPenilaian->lingkup ?? 'Lainnya';
        })->map(function ($group) {
            return round($group->avg('nilai'), 2);
        });

        $hasilAnalisis = HasilAnalisis::where('siswa_id', $siswaId)
            ->where('periode', $periode)
            ->where('tahun_ajaran', $tahunAjaran)
            ->latest()
            ->first();

        // Ambil nilai semester sebelumnya untuk tracking tren
        $previousSemester = $this->getPreviousSemesterNilai($siswaId, $periode, $tahunAjaran);

        $smartAnalysis  = null;
        $clusterProfile = null;
        if ($hasilAnalisis && $nilaiPerLingkup->isNotEmpty()) {
            $rawResponse    = $hasilAnalisis->raw_response;
            $clusterGroup   = $hasilAnalisis->cluster_group;
            $clusterProfile = $rawResponse['profiles'][$clusterGroup] ?? null;

            $smartAnalysis = $this->smartAnalysisService->analyze(
                $nilaiPerLingkup,
                $nilaiRapors,
                $clusterProfile,
                $previousSemester
            );
        }

        return [$nilaiRapors, $nilaiPerLingkup, $hasilAnalisis, $smartAnalysis, $clusterProfile];
    }

    /**
     * Ambil rata-rata nilai per lingkup dari semester sebelumnya untuk analisis tren.
     */
    private function getPreviousSemesterNilai(int $siswaId, string $periode, string $tahunAjaran): ?array
    {
        // Tentukan semester & tahun sebelumnya
        if ($periode === 'Genap') {
            $prevPeriode     = 'Ganjil';
            $prevTahunAjaran = $tahunAjaran; // Ganjil & Genap dalam satu tahun ajaran
        } else {
            // Ganjil sekarang → semester sebelumnya = Genap tahun ajaran lalu
            $prevPeriode = 'Genap';
            $parts       = explode('/', $tahunAjaran);
            if (count($parts) === 2) {
                $prevTahunAjaran = ($parts[0] - 1) . '/' . ($parts[1] - 1);
            } else {
                return null;
            }
        }

        $prevNilai = NilaiRapor::where('siswa_id', $siswaId)
            ->where('periode', $prevPeriode)
            ->where('tahun_ajaran', $prevTahunAjaran)
            ->with('aspekPenilaian')
            ->get();

        if ($prevNilai->isEmpty()) {
            return null;
        }

        return $prevNilai->groupBy(fn ($nr) => $nr->aspekPenilaian->lingkup ?? 'Lainnya')
            ->map(fn ($g) => round($g->avg('nilai'), 2))
            ->toArray();
    }

    /**
     * [M2] Validate tahun ajaran format is consecutive (e.g. 2026/2027).
     */
    private function isValidTahunAjaran(string $tahunAjaran): bool
    {
        $parts = explode('/', $tahunAjaran);
        return count($parts) === 2 && (int) $parts[1] === (int) $parts[0] + 1;
    }

    /**
     * [S-1] Authorization guard — cegah guru akses siswa di luar kelasnya.
     * Guru hanya bisa mengakses siswa yang berada di kelas yang ada di jadwalnya.
     */
    private function authorizeSiswa(Siswa $siswa): void
    {
        $guru = Auth::user()->guru;
        if (!$guru) {
            Log::warning('Rapor access without guru data', ['user_id' => Auth::id()]);
            abort(403, 'Data guru tidak ditemukan.');
        }

        $kelasGuruIds = Jadwal::where('guru_id', $guru->id)
            ->pluck('kelas_id')
            ->unique();

        if (!$kelasGuruIds->contains($siswa->kelas_id)) {
            Log::warning('Unauthorized siswa access attempt', [
                'user_id' => Auth::id(),
                'siswa_id' => $siswa->id,
                'siswa_kelas_id' => $siswa->kelas_id,
                'guru_kelas_ids' => $kelasGuruIds->toArray(),
                'ip' => request()->ip(),
            ]);
            abort(403, 'Anda tidak memiliki akses ke data siswa ini.');
        }
    }
}
