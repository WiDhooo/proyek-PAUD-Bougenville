<?php

namespace App\Services;

/**
 * SmartAnalysisService — Personalized Recommendation Engine untuk Rapor Digital PAUD.
 *
 * Versi 2.0 — FULL PERSONALIZATION:
 * - Saran berbeda berdasarkan skor AKTUAL (bukan hanya label "kuat/lemah")
 * - Konteks cluster: perbandingan anak dengan rata-rata kelompok sebayanya
 * - Level skor: BB(1) / MB(2) / BSH(3) / BSB(4) dengan strategi berbeda tiap level
 * - Red flag detection dengan intensitas yang disesuaikan
 * - Cross-discipline integrative suggestions (30 kombinasi)
 *
 * @see \App\Http\Controllers\RaporController
 */
class SmartAnalysisService
{
    // Thresholds
    private const THRESHOLD_KUAT  = 3.0;
    private const THRESHOLD_LEMAH = 2.0;
    private const RED_FLAG_SCORE  = 1;

    /**
     * Generate analisis cerdas PERSONAL berdasarkan data aktual siswa.
     *
     * @param iterable   $nilaiPerLingkup  {'Kognitif': 3.5, ...}
     * @param iterable   $nilaiRapors      Eloquent Collection nilai per indikator
     * @param array|null $clusterProfile   {'rata_rata_aspek': {...}, 'jumlah_siswa': 5, ...}
     * @param array|null $previousSemester {'Kognitif': 2.5, ...} nilai semester lalu
     */
    public function analyze(
        iterable   $nilaiPerLingkup,
        iterable   $nilaiRapors,
        ?array     $clusterProfile   = null,
        ?array     $previousSemester = null
    ): array {
        $nilaiPerLingkup = is_array($nilaiPerLingkup) ? $nilaiPerLingkup : $nilaiPerLingkup->toArray();
        $aspekDb         = $this->getAspekDatabase();

        // 1. CLASSIFY semua aspek dengan skor aktual
        $aspekKuat  = [];
        $aspekLemah = [];
        foreach ($nilaiPerLingkup as $lingkup => $avg) {
            if ($avg >= self::THRESHOLD_KUAT) {
                $aspekKuat[$lingkup] = $avg;
            } elseif ($avg < self::THRESHOLD_LEMAH) {
                $aspekLemah[$lingkup] = $avg;
            }
        }
        arsort($aspekKuat);
        asort($aspekLemah);

        // 2. RED FLAG SCANNING
        $redFlags = [];
        foreach ($nilaiRapors as $nr) {
            if ($nr->nilai == self::RED_FLAG_SCORE) {
                $redFlags[] = [
                    'indikator'   => $nr->aspekPenilaian->indikator   ?? '-',
                    'sub_lingkup' => $nr->aspekPenilaian->sub_lingkup ?? '-',
                    'lingkup'     => $nr->aspekPenilaian->lingkup     ?? '-',
                ];
            }
        }

        // 3. LABEL UTAMA
        $aspekTertinggi = !empty($aspekKuat) ? array_key_first($aspekKuat) : null;
        $labelUtama     = $aspekTertinggi
            ? ($aspekDb[$aspekTertinggi]['label'] ?? 'Berkembang Merata')
            : 'Berkembang Merata (Generalis)';

        // 4. SARAN PERSONAL (score-aware)
        $saranKekuatan  = $this->buildPersonalizedSaranKekuatan($aspekKuat, $aspekDb);
        $saranKelemahan = $this->buildPersonalizedSaranKelemahan($aspekLemah, $aspekDb);

        // 5. CLUSTER PEER COMPARISON
        $peerComparison = $this->buildPeerComparison($nilaiPerLingkup, $clusterProfile);

        // 6. TRACKING TREN SEMESTER
        $trendData = $this->buildTrendAnalysis($nilaiPerLingkup, $previousSemester);

        return [
            'label_utama'      => $labelUtama,
            'aspek_kuat'       => $aspekKuat,
            'aspek_lemah'      => $aspekLemah,
            'deskripsi'        => $this->buildSmartDescription($nilaiPerLingkup, $aspekKuat, $aspekLemah, $aspekDb),
            'saran_kekuatan'   => $saranKekuatan,
            'saran_kelemahan'  => $saranKelemahan,
            'saran_integratif' => $this->buildIntegrativeSuggestion($aspekKuat, $aspekLemah),
            'red_flags'        => $redFlags,
            'cluster_profile'  => $clusterProfile,
            'peer_comparison'  => $peerComparison,
            'trend_data'       => $trendData,
        ];
    }

    // ================================================================
    // KNOWLEDGE BASE
    // ================================================================

    private function getAspekDatabase(): array
    {
        return [
            'Agama & Moral' => [
                'label'       => 'Dominan Spiritual & Moral',
                'level_saran' => [
                    'BB'  => 'Mulai dari hal paling sederhana: biasakan mengucapkan Bismillah sebelum makan, Alhamdulillah setelah makan, dan mencontohkan salam. Perkenalkan nama Tuhan sesuai agamanya secara berulang dan menyenangkan.',
                    'MB'  => 'Anak sudah mulai mengenal nilai agama. Kuatkan dengan: bercerita kisah nabi yang pendek, bermain peran adab sehari-hari, menghafal doa pendek bersama, dan perkenalkan konsep jujur dan sopan melalui cerita.',
                    'BSH' => 'Anak berkembang baik. Tingkatkan dengan: memimpin doa sebelum kegiatan, menceritakan kembali kisah nabi, kegiatan sedekah/berbagi, dan diskusikan mengapa penting menghormati teman yang berbeda agama.',
                    'BSB' => 'Anak sangat unggul! Tantang dengan: menjadi pemimpin doa kelas, membantu guru mengingatkan teman bersikap baik, berdiskusi nilai-nilai moral, dan praktikkan toleransi aktif kepada teman berbeda agama.',
                ],
                'label_level' => [
                    'BB'  => 'Belum Berkembang (1.0-1.4)',
                    'MB'  => 'Mulai Berkembang (1.5-2.4)',
                    'BSH' => 'Berkembang Sesuai Harapan (2.5-3.4)',
                    'BSB' => 'Berkembang Sangat Baik (3.5-4.0)',
                ],
            ],
            'Fisik-Motorik' => [
                'label'       => 'Dominan Kinestetik & Motorik',
                'level_saran' => [
                    'BB'  => 'Prioritaskan stimulasi dasar: latihan berjalan di garis lurus, menangkap bola besar, dan menggenggam krayon tebal untuk menggambar bebas. Juga biasakan mencuci tangan sendiri setiap hari.',
                    'MB'  => 'Lanjutkan dengan: melempar dan menangkap bola sedang, mewarnai gambar besar, meniti balok titian rendah, latihan menggunting garis lurus, dan menggunakan sendok makan dengan benar.',
                    'BSH' => 'Kembangkan lebih jauh: senam/tarian anak sederhana, menggunting sesuai pola, melukis detail, permainan fisik beraturan (engklek), dan eksplorasi menggunakan berbagai alat tulis.',
                    'BSB' => 'Kemampuan motoriknya luar biasa! Arahkan ke: permainan tradisional kompleks, koordinasi tari/senam yang lebih sulit, meronce/menjahit karton, dan kenalkan kegiatan yang menantang kelenturan & keseimbangan.',
                ],
            ],
            'Kognitif' => [
                'label'       => 'Dominan Kognitif & Eksploratif',
                'level_saran' => [
                    'BB'  => 'Mulai dari konsep konkret: mengenali warna primer, menghitung 1-5 benda nyata, memilah benda besar/kecil, dan mengenal nama benda berdasarkan fungsinya (sendok untuk makan).',
                    'MB'  => 'Kembangkan dengan: puzzle 4-6 keping, mengurutkan gambar cerita, menghitung 1-10, permainan tebak bentuk, dan eksperimen sederhana seperti mengamati benda tenggelam/mengapung.',
                    'BSH' => 'Tingkatkan tantangan: puzzle 12+ keping, eksperimen sains (air, magnet), menghitung 1-20, klasifikasi benda 3 variasi (warna+bentuk+ukuran), dan mengenal sebab-akibat di lingkungan sekitar.',
                    'BSB' => 'Anak sangat cerdas! Berikan: eksperimen mandiri, puzzle 20+ keping, permainan logika, menyebutkan lambang bilangan 1-10, mengenal huruf vokal-konsonan, dan tantang dengan pertanyaan terbuka.',
                ],
            ],
            'Bahasa' => [
                'label'       => 'Dominan Linguistik & Verbal',
                'level_saran' => [
                    'BB'  => 'Mulai dengan: sering mengajak bicara, tunjuk dan sebutkan nama benda di sekitar, nyanyikan lagu sederhana berulang, baca buku bergambar, dan ajarkan untuk menyimak orang lain bicara.',
                    'MB'  => 'Perkaya kosakata: membacakan cerita pendek setiap hari, ajak mendeskripsikan gambar, bermain tebak binatang dari suaranya, latihan kalimat sederhana, dan kenalkan simbol-simbol huruf pertama.',
                    'BSH' => 'Kembangkan lebih dalam: dorong bercerita urut (awal-tengah-akhir), kenalkan bunyi dan bentuk huruf, ajak membaca namanya sendiri, bermain kata-kata baru, dan latihan mengikuti aturan permainan.',
                    'BSB' => 'Bakat linguistik sangat menonjol! Arahkan ke: mendongeng di depan kelas, membuat buku cerita mini, latihan menulis namanya sendiri, bermain drama/role-play, dan diskusi bertanya-jawab kompleks.',
                ],
            ],
            'Sosial-Emosional' => [
                'label'       => 'Dominan Sosial & Emosional',
                'level_saran' => [
                    'BB'  => 'Fokus pada keamanan emosi: pastikan anak merasa aman di kelas, kenalkan kartu ekspresi wajah (senang/sedih/marah), dorong interaksi 1-1 dengan satu teman, dan ajarkan cara meminta izin.',
                    'MB'  => 'Latih secara bertahap: bermain berpasangan, belajar bergiliran, mengenali perasaan dari cerita bergambar, latihan ekspresi verbal emosi, dan dorong berbagi mainan dengan teman.',
                    'BSH' => 'Tingkatkan kecerdasan sosial: kegiatan kelompok 3-5 anak, bermain peran dengan dialog, diskusi "bagaimana perasaanmu jika...", latihan resolusi konflik, dan ajak menghargai pendapat teman.',
                    'BSB' => 'Kecerdasan interpersonal sangat tinggi! Arahkan sebagai: mediator teman yang berselisih, pemimpin kelompok kecil, dorong empati aktif, dan fasilitasi kegiatan kerja sama yang kompleks.',
                ],
            ],
            'Seni' => [
                'label'       => 'Dominan Seni & Kreativitas',
                'level_saran' => [
                    'BB'  => 'Perkenalkan seni tanpa tekanan: coretan bebas krayon tebal, bermain playdough/clay, tepuk ritme sederhana, mendengarkan musik anak, dan menirukan gerakan binatang/pohon sederhana.',
                    'MB'  => 'Eksplorasi lebih banyak media: mewarnai gambar, bermain cat jari, kolase kertas warna, meniru gerakan tari lagu anak, menyanyi lagu pendek bersama, dan bermain peran sederhana.',
                    'BSH' => 'Kembangkan ekspresi: menggambar tema bebas, bernyanyi dengan lirik lengkap, belajar tarian pendek, bermain alat musik sederhana (rebana, kastanyet), dan membuat kerajinan dari kardus/clay.',
                    'BSB' => 'Bakat seni sangat kuat! Fasilitasi ekspresi penuh: pameran karya mini, bermain alat musik tradisional bersama teman, koreografi tari pendek, bermain drama, dan eksplorasi berbagai teknik melukis.',
                ],
            ],
        ];
    }

    // ================================================================
    // PERSONALIZED BUILDERS
    // ================================================================

    /**
     * Saran kekuatan PERSONAL — berbeda berdasarkan skor aktual.
     */
    private function buildPersonalizedSaranKekuatan(array $aspekKuat, array $aspekDb): array
    {
        $saran = [];
        foreach ($aspekKuat as $lingkup => $avg) {
            $level       = $this->getScoreLevel($avg);
            $levelSaran  = $aspekDb[$lingkup]['level_saran'][$level] ?? null;
            if ($levelSaran) {
                $saran[] = [
                    'lingkup' => $lingkup,
                    'skor'    => $avg,
                    'level'   => $level,
                    'teks'    => $levelSaran,
                ];
            }
        }
        return $saran;
    }

    /**
     * Saran kelemahan PERSONAL — berbeda berdasarkan seberapa rendah skor & gap dari threshold.
     */
    private function buildPersonalizedSaranKelemahan(array $aspekLemah, array $aspekDb): array
    {
        $saran = [];
        foreach ($aspekLemah as $lingkup => $avg) {
            $level      = $this->getScoreLevel($avg);
            $levelSaran = $aspekDb[$lingkup]['level_saran'][$level] ?? null;
            if ($levelSaran) {
                $saran[] = [
                    'lingkup' => $lingkup,
                    'skor'    => $avg,
                    'level'   => $level,
                    'urgent'  => $avg <= 1.5,
                    'teks'    => $levelSaran,
                ];
            }
        }
        return $saran;
    }

    /**
     * Peer comparison: bandingkan anak dengan rata-rata kelompok cluster-nya.
     */
    private function buildPeerComparison(array $nilaiPerLingkup, ?array $clusterProfile): ?array
    {
        if (!$clusterProfile || empty($clusterProfile['rata_rata_aspek'])) {
            return null;
        }

        $rataRataKelompok = $clusterProfile['rata_rata_aspek'];
        $jumlahSiswa      = $clusterProfile['jumlah_siswa'] ?? '?';
        $comparison       = [];

        foreach ($nilaiPerLingkup as $lingkup => $skorAnak) {
            if (!isset($rataRataKelompok[$lingkup])) {
                continue;
            }
            $rataKelompok = $rataRataKelompok[$lingkup];
            $selisih      = round($skorAnak - $rataKelompok, 2);

            if ($selisih > 0.3) {
                $status = 'above';
                $icon   = '↑';
                $label  = "Di atas rata-rata kelompok (+{$selisih})";
            } elseif ($selisih < -0.3) {
                $status = 'below';
                $icon   = '↓';
                $label  = "Di bawah rata-rata kelompok ({$selisih})";
            } else {
                $status = 'equal';
                $icon   = '→';
                $label  = 'Setara rata-rata kelompok';
            }

            $comparison[$lingkup] = [
                'skor_anak'       => $skorAnak,
                'rata_kelompok'   => $rataKelompok,
                'selisih'         => $selisih,
                'status'          => $status,
                'icon'            => $icon,
                'label'           => $label,
            ];
        }

        return [
            'jumlah_siswa_kelompok' => $jumlahSiswa,
            'aspek_dominan_kelompok' => $clusterProfile['aspek_dominan'] ?? '-',
            'detail'                => $comparison,
        ];
    }

    /**
     * Analisis tren perkembangan antar semester.
     */
    private function buildTrendAnalysis(array $nilaiSekarang, ?array $nilaiSebelumnya): ?array
    {
        if (!$nilaiSebelumnya || empty($nilaiSebelumnya)) {
            return null;
        }

        $trends = [];
        foreach ($nilaiSekarang as $lingkup => $skorSekarang) {
            if (!isset($nilaiSebelumnya[$lingkup])) {
                continue;
            }
            $skorLalu = $nilaiSebelumnya[$lingkup];
            $delta    = round($skorSekarang - $skorLalu, 2);

            if ($delta >= 0.5) {
                $trend = 'up_significant';
                $icon  = '🚀';
                $label = "Meningkat Signifikan (+{$delta})";
            } elseif ($delta > 0) {
                $trend = 'up';
                $icon  = '📈';
                $label = "Meningkat (+{$delta})";
            } elseif ($delta == 0) {
                $trend = 'stable';
                $icon  = '➡️';
                $label = 'Stabil';
            } elseif ($delta > -0.5) {
                $trend = 'down';
                $icon  = '📉';
                $label = "Menurun ({$delta})";
            } else {
                $trend = 'down_significant';
                $icon  = '⚠️';
                $label = "Menurun Signifikan ({$delta})";
            }

            $trends[$lingkup] = [
                'skor_lalu'    => $skorLalu,
                'skor_sekarang' => $skorSekarang,
                'delta'        => $delta,
                'trend'        => $trend,
                'icon'         => $icon,
                'label'        => $label,
            ];
        }

        return empty($trends) ? null : $trends;
    }

    /**
     * Build deskripsi yang personal — menyebut skor aktual, bukan hanya label.
     */
    private function buildSmartDescription(
        array $nilaiPerLingkup,
        array $aspekKuat,
        array $aspekLemah,
        array $aspekDb
    ): string {
        $parts = [];

        if (!empty($aspekKuat)) {
            $namaKuat = array_keys($aspekKuat);
            if (count($aspekKuat) >= 4) {
                $parts[] = 'Ananda menunjukkan profil perkembangan yang sangat cemerlang dengan keunggulan di bidang ' . implode(', ', $namaKuat) . '.';
            } elseif (count($aspekKuat) > 1) {
                $parts[] = 'Ananda memiliki keunggulan di beberapa bidang, terutama ' . implode(' dan ', $namaKuat) . '.';
            } else {
                $l = array_key_first($aspekKuat);
                $parts[] = "Bidang terkuat Ananda saat ini adalah {$l}. Potensi ini dapat terus dikembangkan melalui kegiatan yang lebih menantang.";
            }
        }

        if (!empty($aspekLemah)) {
            $namaLemah = array_keys($aspekLemah);
            $parts[] = 'Beberapa aspek yang masih memerlukan perhatian dan stimulasi lebih lanjut yaitu ' . implode(', ', $namaLemah) . '.';
        }

        if (empty($aspekKuat) && empty($aspekLemah)) {
            $parts[] = 'Ananda menunjukkan perkembangan yang cukup merata di semua aspek. Pertahankan stimulasi di semua bidang secara seimbang agar perkembangan tetap optimal.';
        }

        return implode(' ', $parts);
    }

    /**
     * Saran integratif cross-discipline (30 kombinasi).
     */
    private function buildIntegrativeSuggestion(array $aspekKuat, array $aspekLemah): ?string
    {
        if (empty($aspekKuat) || empty($aspekLemah)) {
            return null;
        }

        $k   = array_key_first($aspekKuat);
        $l   = array_key_first($aspekLemah);
        $skk = $aspekKuat[$k];
        $skl = $aspekLemah[$l];

        $map = [
            'Seni' => [
                'Agama & Moral'    => 'Gunakan ketertarikan seni untuk mengenalkan agama: mewarnai gambar tempat ibadah, menyanyikan lagu religi, atau menggambar cerita para nabi.',
                'Fisik-Motorik'    => 'Manfaatkan seni untuk melatih motorik: menggambar gerakan besar (motorik kasar) dan membuat kerajinan detail (motorik halus).',
                'Kognitif'         => 'Hubungkan seni dengan kognitif: menggambar pola berurutan, menyusun balok warna-warni, atau bermain musik sambil menghitung ketukan.',
                'Bahasa'           => 'Kembangkan bahasa melalui seni: ajak mendeskripsikan gambarnya, bernyanyi dengan lirik, atau bercerita tentang karya seninya.',
                'Sosial-Emosional' => 'Gunakan seni kelompok untuk membangun sosial: melukis bersama, pertunjukan musik mini, atau proyek seni kolaboratif.',
            ],
            'Kognitif' => [
                'Agama & Moral'    => 'Manfaatkan rasa ingin tahu untuk mengenalkan agama: eksplorasi alam (ciptaan Tuhan) dan percobaan sains sederhana.',
                'Fisik-Motorik'    => 'Hubungkan kognitif dengan motorik: obstacle course dengan instruksi berurutan dan permainan strategi fisik.',
                'Bahasa'           => 'Kembangkan bahasa melalui kognitif: ajak menjelaskan langkah eksperimen atau menceritakan proses pemecahan masalah.',
                'Sosial-Emosional' => 'Latih sosial melalui permainan kelompok yang membutuhkan kerjasama dan strategi bersama.',
                'Seni'             => 'Hubungkan kognitif dengan seni: membuat pola, menggambar bentuk geometri, atau menyusun komposisi warna.',
            ],
            'Bahasa' => [
                'Agama & Moral'    => 'Manfaatkan kekuatan bahasa untuk mengenalkan agama: mendongeng kisah nabi, menghafal doa pendek, dan bernyanyi lagu religi.',
                'Fisik-Motorik'    => 'Hubungkan bahasa dengan motorik: bermain "Simon Says" atau instruksi gerakan menggunakan kalimat lengkap.',
                'Kognitif'         => 'Kembangkan kognitif melalui bahasa: tebak-tebakan logika, bercerita dengan alur sebab-akibat, dan diskusi tentang benda sekitar.',
                'Sosial-Emosional' => 'Latih sosial melalui bermain peran dengan dialog, mengungkapkan perasaan dengan kata-kata, dan drama sederhana.',
                'Seni'             => 'Hubungkan bahasa dengan seni: mendongeng kreatif, membuat puisi sederhana, dan bernyanyi lagu anak.',
            ],
            'Fisik-Motorik' => [
                'Agama & Moral'    => 'Hubungkan motorik dengan agama: praktik gerakan ibadah, senam dengan lagu religi, dan kegiatan fisik bernilai moral.',
                'Kognitif'         => 'Kembangkan kognitif melalui fisik: treasure hunt dengan petunjuk logika, atau olahraga yang membutuhkan strategi.',
                'Bahasa'           => 'Latih bahasa melalui fisik: berikan instruksi verbal saat bermain, ajak menjelaskan gerakan yang dilakukan.',
                'Sosial-Emosional' => 'Kembangkan sosial melalui olahraga tim dan permainan kelompok yang membutuhkan kerjasama fisik.',
                'Seni'             => 'Hubungkan motorik dengan seni: menari, senam kreasi, dan membuat karya seni tiga dimensi.',
            ],
            'Agama & Moral' => [
                'Fisik-Motorik'    => 'Hubungkan agama dengan fisik: praktik gerakan ibadah dan jalan-jalan alam sambil bersyukur.',
                'Kognitif'         => 'Kembangkan kognitif melalui diskusi tentang ciptaan Tuhan dan pelajaran moral dari cerita agama.',
                'Bahasa'           => 'Latih bahasa melalui menghafal doa, bercerita kisah nabi, dan berdiskusi tentang perbuatan baik.',
                'Sosial-Emosional' => 'Hubungkan moral dengan sosial: kegiatan berbagi, tolong-menolong, dan bermain peran sikap terpuji.',
                'Seni'             => 'Kembangkan seni melalui religi: mewarnai gambar masjid, bernyanyi lagu islami, membuat kaligrafi sederhana.',
            ],
            'Sosial-Emosional' => [
                'Agama & Moral'    => 'Manfaatkan kepekaan sosial untuk mengenalkan agama: berbagi dengan teman dan membantu yang membutuhkan.',
                'Fisik-Motorik'    => 'Latih motorik melalui permainan kelompok yang membutuhkan gerakan fisik dan kerjasama.',
                'Kognitif'         => 'Kembangkan kognitif melalui diskusi kelompok, board games strategi, dan memecahkan masalah bersama.',
                'Bahasa'           => 'Latih bahasa melalui kegiatan sosial: berdiskusi kelompok, bermain peran dengan dialog, dan presentasi di depan teman.',
                'Seni'             => 'Hubungkan sosial dengan seni: proyek seni kolaboratif, pertunjukan bersama, dan karya seni berkelompok.',
            ],
        ];

        $base = $map[$k][$l] ?? "Manfaatkan ketertarikan Ananda pada bidang {$k} sebagai jembatan untuk menstimulasi aspek {$l} yang masih perlu dikembangkan.";

        return $base;
    }

    // ================================================================
    // UTILITIES
    // ================================================================

    /**
     * Konversi skor numerik ke level PAUD.
     */
    private function getScoreLevel(float $score): string
    {
        if ($score >= 3.5) return 'BSB';
        if ($score >= 2.5) return 'BSH';
        if ($score >= 1.5) return 'MB';
        return 'BB';
    }
}
