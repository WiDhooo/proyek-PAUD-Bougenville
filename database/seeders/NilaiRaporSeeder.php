<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AspekPenilaian;
use App\Models\NilaiRapor;
use Carbon\Carbon;

class NilaiRaporSeeder extends Seeder
{
    /**
     * 5 Profil Cluster yang dirancang dengan KONTRAS MAKSIMAL.
     *
     * Prinsip desain:
     * - Aspek DOMINAN  → skor tinggi: rand(3, 4)
     * - Aspek LEMAH    → skor rendah: rand(1, 2)
     * - Aspek SEDANG   → skor tengah: rand(2, 3)
     *
     * Kontras besar (tinggi vs rendah) = Manhattan distance besar
     * = ML Silhouette Score tinggi = lebih dari 3 cluster terdeteksi.
     *
     * 20 siswa per kelas, 4 siswa per profil.
     */
    private const PROFILES = [
        // Profil 0 — Dominan Kinestetik & Motorik
        // Karakter: aktif secara fisik, suka bergerak, belum kuat akademis
        0 => [
            'Fisik-Motorik'    => [3, 4],   // TINGGI
            'Agama & Moral'    => [1, 2],   // RENDAH
            'Kognitif'         => [1, 2],   // RENDAH
            'Bahasa'           => [1, 2],   // RENDAH
            'Sosial-Emosional' => [2, 3],   // SEDANG
            'Seni'             => [1, 2],   // RENDAH
        ],

        // Profil 1 — Dominan Spiritual & Sosial
        // Karakter: empatis, religius, suka bermain bersama
        1 => [
            'Agama & Moral'    => [3, 4],   // TINGGI
            'Sosial-Emosional' => [3, 4],   // TINGGI
            'Fisik-Motorik'    => [1, 2],   // RENDAH
            'Kognitif'         => [1, 2],   // RENDAH
            'Bahasa'           => [2, 3],   // SEDANG
            'Seni'             => [1, 2],   // RENDAH
        ],

        // Profil 2 — Dominan Kognitif & Verbal
        // Karakter: analitis, rasa ingin tahu tinggi, pandai bercerita
        2 => [
            'Kognitif'         => [3, 4],   // TINGGI
            'Bahasa'           => [3, 4],   // TINGGI
            'Fisik-Motorik'    => [1, 2],   // RENDAH
            'Agama & Moral'    => [2, 3],   // SEDANG
            'Sosial-Emosional' => [1, 2],   // RENDAH
            'Seni'             => [1, 2],   // RENDAH
        ],

        // Profil 3 — Dominan Seni & Kreativitas
        // Karakter: imajinatif, ekspresif, suka menggambar dan menyanyi
        3 => [
            'Seni'             => [3, 4],   // TINGGI
            'Agama & Moral'    => [1, 2],   // RENDAH
            'Fisik-Motorik'    => [2, 3],   // SEDANG
            'Kognitif'         => [1, 2],   // RENDAH
            'Bahasa'           => [1, 2],   // RENDAH
            'Sosial-Emosional' => [1, 2],   // RENDAH
        ],

        // Profil 4 — Generalis (Serba Bisa)
        // Karakter: berkembang merata di semua aspek, tidak ada kelemahan mencolok
        // Profil ini PALING JAUH dari profil 0-3 sehingga pasti jadi cluster sendiri.
        4 => [
            'Agama & Moral'    => [3, 4],   // TINGGI semua
            'Fisik-Motorik'    => [3, 4],   // TINGGI semua
            'Kognitif'         => [3, 4],   // TINGGI semua
            'Bahasa'           => [3, 4],   // TINGGI semua
            'Sosial-Emosional' => [3, 4],   // TINGGI semua
            'Seni'             => [3, 4],   // TINGGI semua
        ],
    ];

    public function run()
    {
        $now   = Carbon::now();
        $aspeks = AspekPenilaian::all();

        if ($aspeks->isEmpty()) {
            $this->command->warn('Tidak ada data AspekPenilaian. Jalankan AspekPenilaianSeeder terlebih dahulu.');
            return;
        }

        $kelasIds = Kelas::pluck('id')->toArray();
        $nilaiData = [];

        foreach ($kelasIds as $kelasId) {
            // Ambil siswa per kelas, diurutkan konsisten
            $siswas = Siswa::where('kelas_id', $kelasId)->orderBy('id')->get();

            foreach ($siswas as $indexInClass => $siswa) {
                // Tentukan profil berdasarkan posisi siswa di dalam kelas
                // 4 siswa pertama = Profil 0, 4 berikutnya = Profil 1, dst.
                $profilIndex = (int) floor($indexInClass / 4);
                // Pastikan tidak melebihi jumlah profil yang tersedia
                $profilIndex = min($profilIndex, count(self::PROFILES) - 1);
                $profil = self::PROFILES[$profilIndex];

                foreach ($aspeks as $aspek) {
                    $lingkup = $aspek->lingkup;

                    // Ambil rentang nilai dari profil, fallback ke [2, 3] jika lingkup tidak terdefinisi
                    $range = $profil[$lingkup] ?? [2, 3];
                    $nilai = rand($range[0], $range[1]);

                    $nilaiData[] = [
                        'siswa_id'          => $siswa->id,
                        'aspek_penilaian_id' => $aspek->id,
                        'nilai'             => $nilai,
                        'periode'           => 'Ganjil',
                        'tahun_ajaran'      => '2026/2027',
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
            }
        }

        // Insert chunked agar cepat dan tidak memory limit
        foreach (array_chunk($nilaiData, 500) as $chunk) {
            NilaiRapor::insert($chunk);
        }

        $totalSiswa = count($kelasIds) * 20;
        $this->command->info("✅ NilaiRaporSeeder: {$totalSiswa} siswa, 5 profil cluster, Ganjil 2026/2027.");
    }
}
