<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AspekPenilaian;
use App\Models\NilaiRapor;
use Carbon\Carbon;

/**
 * NilaiRaporGenapSeeder
 *
 * Menyemai nilai rapor Semester GENAP 2026/2027 untuk KELAS A saja.
 *
 * Tujuan utama: mengaktifkan fitur TREN PERKEMBANGAN ANTAR SEMESTER
 * di halaman detail rapor, karena fitur ini memerlukan data semester
 * sebelumnya (Ganjil) DAN semester berjalan (Genap) dari siswa yang sama.
 *
 * Desain nilai:
 *  - Profil 0 (Kinestetik)  : Fisik-Motorik naik (3→4), Kognitif naik sedikit (1→2)
 *  - Profil 1 (Spiritual)   : Agama naik (3→4), Fisik-Motorik tetap rendah
 *  - Profil 2 (Kognitif)    : Kognitif naik (3→4), Bahasa stabil (3→3)
 *  - Profil 3 (Seni)        : Seni naik (3→4), ada regresi di Sosial-Emosional (2→1)
 *  - Profil 4 (Generalis)   : Semua aspek tetap tinggi (3→4), stabil
 *
 * Ini menghasilkan variasi tren: naik signifikan, naik, stabil, turun
 * → data yang menarik untuk demonstrasi fitur tren di rapor.
 */
class NilaiRaporGenapSeeder extends Seeder
{
    /**
     * Profil GENAP — nilai sedikit bergeser dari Profil GANJIL
     * untuk menciptakan tren yang realistis dan bervariasi.
     */
    private const PROFILES_GENAP = [
        // Profil 0 — Kinestetik: Fisik naik drastis, Kognitif mulai tumbuh
        0 => [
            'Fisik-Motorik'    => [4, 4],   // NAIK SIGNIFIKAN (was 3-4, now 4)
            'Agama & Moral'    => [1, 2],   // STABIL
            'Kognitif'         => [2, 3],   // NAIK (was 1-2, now 2-3)
            'Bahasa'           => [1, 2],   // STABIL
            'Sosial-Emosional' => [2, 3],   // STABIL
            'Seni'             => [2, 3],   // NAIK SEDIKIT
        ],

        // Profil 1 — Spiritual: semakin kuat di nilai agama & sosial
        1 => [
            'Agama & Moral'    => [4, 4],   // NAIK SIGNIFIKAN
            'Sosial-Emosional' => [3, 4],   // NAIK
            'Fisik-Motorik'    => [1, 2],   // STABIL
            'Kognitif'         => [2, 3],   // NAIK SEDIKIT
            'Bahasa'           => [2, 3],   // NAIK SEDIKIT
            'Seni'             => [1, 2],   // STABIL
        ],

        // Profil 2 — Kognitif: otak makin terasah, fisik masih tertinggal
        2 => [
            'Kognitif'         => [4, 4],   // NAIK SIGNIFIKAN
            'Bahasa'           => [3, 4],   // NAIK
            'Fisik-Motorik'    => [1, 2],   // STABIL
            'Agama & Moral'    => [2, 3],   // STABIL
            'Sosial-Emosional' => [2, 3],   // NAIK (was 1-2)
            'Seni'             => [1, 2],   // STABIL
        ],

        // Profil 3 — Seni: seni makin menonjol, tapi Sosial ada regresi kecil
        3 => [
            'Seni'             => [4, 4],   // NAIK SIGNIFIKAN
            'Agama & Moral'    => [2, 3],   // NAIK SEDIKIT
            'Fisik-Motorik'    => [2, 3],   // STABIL
            'Kognitif'         => [1, 2],   // STABIL
            'Bahasa'           => [2, 3],   // NAIK SEDIKIT
            'Sosial-Emosional' => [1, 1],   // TURUN (was 1-2, now turun ke 1)
        ],

        // Profil 4 — Generalis: sudah tinggi, tetap konsisten di level tertinggi
        4 => [
            'Agama & Moral'    => [4, 4],   // STABIL TINGGI
            'Fisik-Motorik'    => [3, 4],   // STABIL TINGGI
            'Kognitif'         => [4, 4],   // STABIL TINGGI
            'Bahasa'           => [3, 4],   // STABIL TINGGI
            'Sosial-Emosional' => [3, 4],   // STABIL TINGGI
            'Seni'             => [3, 4],   // STABIL TINGGI
        ],
    ];

    public function run(): void
    {
        $now = Carbon::now();

        // Ambil hanya Kelas A (ID=1, nama "TK A (Matahari)")
        $kelasA = Kelas::where('nama_kelas', 'like', '%A%')
            ->orderBy('id')
            ->first();

        if (!$kelasA) {
            $this->command->error('❌ Kelas A tidak ditemukan. Pastikan KelasSeeder sudah dijalankan.');
            return;
        }

        $aspeks = AspekPenilaian::all();

        if ($aspeks->isEmpty()) {
            $this->command->error('❌ Tidak ada data AspekPenilaian. Jalankan AspekPenilaianSeeder terlebih dahulu.');
            return;
        }

        // Cek apakah data Genap sudah ada agar tidak duplikat
        $alreadyExists = NilaiRapor::where('periode', 'Genap')
            ->where('tahun_ajaran', '2026/2027')
            ->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasA->id))
            ->exists();

        if ($alreadyExists) {
            $this->command->warn("⚠️  Data nilai Genap 2026/2027 untuk Kelas {$kelasA->nama_kelas} sudah ada. Seeder dilewati.");
            $this->command->warn("    Jalankan: php artisan migrate:fresh --seed  untuk reset total.");
            return;
        }

        $siswas = Siswa::where('kelas_id', $kelasA->id)->orderBy('id')->get();

        if ($siswas->isEmpty()) {
            $this->command->error("❌ Tidak ada siswa di Kelas {$kelasA->nama_kelas}.");
            return;
        }

        $nilaiData = [];

        foreach ($siswas as $indexInClass => $siswa) {
            // Profil 4 siswa per profil, persis seperti NilaiRaporSeeder (Ganjil)
            $profilIndex = (int) floor($indexInClass / 4);
            $profilIndex = min($profilIndex, count(self::PROFILES_GENAP) - 1);
            $profil      = self::PROFILES_GENAP[$profilIndex];

            foreach ($aspeks as $aspek) {
                $lingkup = $aspek->lingkup;
                $range   = $profil[$lingkup] ?? [2, 3];
                $nilai   = rand($range[0], $range[1]);

                $nilaiData[] = [
                    'siswa_id'           => $siswa->id,
                    'aspek_penilaian_id' => $aspek->id,
                    'nilai'              => $nilai,
                    'periode'            => 'Genap',
                    'tahun_ajaran'       => '2026/2027',
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }
        }

        // Chunked insert
        foreach (array_chunk($nilaiData, 500) as $chunk) {
            NilaiRapor::insert($chunk);
        }

        $this->command->info("✅ NilaiRaporGenapSeeder: {$siswas->count()} siswa di Kelas {$kelasA->nama_kelas}, Genap 2026/2027.");
        $this->command->info("   → Tren yang terseed: naik signifikan (Profil 0-3) + stabil (Profil 4 Generalis).");
        $this->command->info("   → Buka rapor siswa Kelas A → pilih Genap 2026/2027 → lihat tren antar semester.");
    }
}
