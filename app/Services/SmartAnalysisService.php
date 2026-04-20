<?php

namespace App\Services;

/**
 * SmartAnalysisService — Expert System Engine untuk Rapor Digital PAUD.
 *
 * Menggantikan static mapping dengan analisis data-driven:
 * - Multi-talent detection (semua aspek >= 3.0 = kekuatan)
 * - Weakness detection (aspek < 2.0 = kelemahan)
 * - Red flag scanning (sub-indikator skor 1 = peringatan kritis)
 * - Data-validated descriptions (klaim hanya muncul jika data mendukung)
 * - Cross-discipline integrative suggestions (30 kombinasi)
 *
 * @see \App\Http\Controllers\RaporController
 */
class SmartAnalysisService
{
    // Threshold constants — mudah di-tune tanpa ubah logic
    private const THRESHOLD_KUAT = 3.0;
    private const THRESHOLD_LEMAH = 2.0;
    private const RED_FLAG_SCORE = 1;
    private const MULTI_TALENT_MIN = 4;

    /**
     * Generate analisis cerdas berdasarkan data aktual siswa.
     *
     * @param iterable $nilaiPerLingkup  Rata-rata per lingkup {"Agama & Moral": 3.5, ...}
     * @param iterable $nilaiRapors      Nilai mentah per sub-indikator (Eloquent Collection)
     * @param array|null $clusterProfile Profil cluster dari Python ML
     * @return array Structured smart analysis result
     */
    public function analyze(iterable $nilaiPerLingkup, iterable $nilaiRapors, ?array $clusterProfile = null): array
    {
        $aspekData = $this->getAspekDatabase();

        // 1. MULTI-TALENT DETECTION
        $aspekKuat = [];
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

        // 2. RED FLAG SCANNING: sub-indikator dengan skor = 1
        $redFlags = [];
        foreach ($nilaiRapors as $nr) {
            if ($nr->nilai == self::RED_FLAG_SCORE) {
                $redFlags[] = [
                    'indikator' => $nr->aspekPenilaian->indikator ?? '-',
                    'sub_lingkup' => $nr->aspekPenilaian->sub_lingkup ?? '-',
                    'lingkup' => $nr->aspekPenilaian->lingkup ?? '-',
                ];
            }
        }

        // 3. LABEL UTAMA
        $aspekTertinggi = !empty($aspekKuat) ? array_key_first($aspekKuat) : null;
        $labelUtama = $aspekTertinggi
            ? ($aspekData[$aspekTertinggi]['label'] ?? 'Berkembang Merata')
            : 'Berkembang Merata (Generalis)';

        return [
            'label_utama'      => $labelUtama,
            'aspek_kuat'       => $aspekKuat,
            'aspek_lemah'      => $aspekLemah,
            'deskripsi'        => $this->buildSmartDescription($aspekKuat, $aspekLemah, $aspekData),
            'saran_kekuatan'   => $this->collectSaran($aspekKuat, $aspekData, 'saran_kekuatan'),
            'saran_kelemahan'  => $this->collectSaran($aspekLemah, $aspekData, 'saran_kelemahan'),
            'saran_integratif' => $this->buildIntegrativeSuggestion($aspekKuat, $aspekLemah),
            'red_flags'        => $redFlags,
            'cluster_profile'  => $clusterProfile,
        ];
    }

    // ================================================================
    // KNOWLEDGE BASE
    // ================================================================

    private function getAspekDatabase(): array
    {
        return [
            'Agama & Moral' => [
                'label' => 'Dominan Spiritual & Moral',
                'deskripsi_kuat' => 'Anak menunjukkan pemahaman agama dan perilaku moral yang baik.',
                'saran_kekuatan' => 'Kembangkan potensi spiritual dengan bercerita kisah nabi, memimpin doa bersama, dan kegiatan berbagi/sedekah.',
                'saran_kelemahan' => 'Stimulasi aspek Agama & Moral: ajak anak meniru gerakan ibadah sederhana, kenalkan konsep Tuhan melalui alam, dan biasakan mengucapkan salam.',
            ],
            'Fisik-Motorik' => [
                'label' => 'Dominan Kinestetik & Motorik',
                'deskripsi_kuat' => 'Anak memiliki koordinasi motorik yang baik dan aktif secara fisik (kecerdasan kinestetik).',
                'saran_kekuatan' => 'Kembangkan bakat kinestetik dengan olahraga mini, senam anak, outbound, dan kegiatan menggambar/mewarnai untuk motorik halus.',
                'saran_kelemahan' => 'Stimulasi Fisik-Motorik: latihan lempar-tangkap bola, meniti balok titian, bersepeda, dan meronce/menggunting untuk motorik halus.',
            ],
            'Kognitif' => [
                'label' => 'Dominan Kognitif & Eksploratif',
                'deskripsi_kuat' => 'Anak cepat menangkap konsep baru dan menunjukkan kemampuan berpikir logis (kecerdasan logis-matematis).',
                'saran_kekuatan' => 'Kembangkan kognitif dengan puzzle kompleks, eksperimen sains sederhana, permainan hitung-hitungan, dan pengelompokan benda.',
                'saran_kelemahan' => 'Stimulasi Kognitif: bermain sortir warna/bentuk, menghitung benda sehari-hari, dan tebak-tebakan sederhana.',
            ],
            'Bahasa' => [
                'label' => 'Dominan Linguistik & Verbal',
                'deskripsi_kuat' => 'Anak memiliki kemampuan verbal yang baik dan aktif berkomunikasi (kecerdasan linguistik).',
                'saran_kekuatan' => 'Kembangkan bakat linguistik dengan membacakan cerita, bermain tebak kata, mendongeng, dan mengenal huruf melalui permainan.',
                'saran_kelemahan' => 'Stimulasi Bahasa: sering mengajak bicara, membacakan buku bergambar, bernyanyi bersama, dan melatih mengulang kalimat sederhana.',
            ],
            'Sosial-Emosional' => [
                'label' => 'Dominan Sosial & Emosional',
                'deskripsi_kuat' => 'Anak memiliki kepekaan sosial dan emosi yang baik, mudah berempati (kecerdasan interpersonal).',
                'saran_kekuatan' => 'Kembangkan kecerdasan interpersonal dengan kegiatan kelompok, bermain peran, kerja tim, dan mengekspresikan emosi.',
                'saran_kelemahan' => 'Stimulasi Sosial-Emosional: bermain bersama teman sebaya, latihan berbagi, dan mengenali emosi melalui gambar ekspresi wajah.',
            ],
            'Seni' => [
                'label' => 'Dominan Seni & Kreativitas',
                'deskripsi_kuat' => 'Anak sangat imajinatif dan mengekspresikan diri melalui seni visual, musik, dan gerakan (kecerdasan musikal & spasial).',
                'saran_kekuatan' => 'Kembangkan bakat seni dengan alat menggambar/melukis, bermain musik/perkusi, menari, dan bermain playdough/clay.',
                'saran_kelemahan' => 'Stimulasi Seni: ajak mendengarkan musik, mewarnai gambar sederhana, dan bermain dengan bahan kreatif seperti clay.',
            ],
        ];
    }

    // ================================================================
    // BUILDERS
    // ================================================================

    /**
     * Collect saran from knowledge base for given aspects.
     */
    private function collectSaran(array $aspects, array $aspekData, string $key): array
    {
        $saran = [];
        foreach ($aspects as $lingkup => $avg) {
            if (isset($aspekData[$lingkup][$key])) {
                $saran[] = $aspekData[$lingkup][$key];
            }
        }
        return $saran;
    }

    /**
     * Build data-validated description (anti-hallucination).
     */
    private function buildSmartDescription(array $aspekKuat, array $aspekLemah, array $aspekData): string
    {
        $parts = [];

        if (!empty($aspekKuat)) {
            $namaKuat = array_map(fn ($l, $a) => "{$l} ({$a})", array_keys($aspekKuat), $aspekKuat);
            if (count($aspekKuat) >= self::MULTI_TALENT_MIN) {
                $parts[] = "Anak menunjukkan profil perkembangan yang sangat cemerlang dengan keunggulan di " . count($aspekKuat) . " bidang: " . implode(', ', $namaKuat) . ".";
            } elseif (count($aspekKuat) > 1) {
                $parts[] = "Anak memiliki keunggulan di beberapa bidang: " . implode(', ', $namaKuat) . ".";
            } else {
                $l = array_key_first($aspekKuat);
                $parts[] = $aspekData[$l]['deskripsi_kuat'] ?? "Anak unggul di bidang {$l}.";
            }
        }

        if (!empty($aspekLemah)) {
            $namaLemah = array_map(fn ($l, $a) => "{$l} ({$a})", array_keys($aspekLemah), $aspekLemah);
            $parts[] = "Aspek yang perlu perhatian khusus: " . implode(', ', $namaLemah) . ".";
        }

        if (empty($aspekKuat) && empty($aspekLemah)) {
            $parts[] = "Anak menunjukkan perkembangan yang relatif merata di semua aspek.";
        }

        return implode(' ', $parts);
    }

    /**
     * Build cross-discipline integrative suggestion (30 combinations).
     */
    private function buildIntegrativeSuggestion(array $aspekKuat, array $aspekLemah): ?string
    {
        if (empty($aspekKuat) || empty($aspekLemah)) {
            return null;
        }

        $k = array_key_first($aspekKuat);
        $l = array_key_first($aspekLemah);

        $map = [
            'Seni' => [
                'Agama & Moral' => 'Gunakan ketertarikan seni untuk mengenalkan agama: mewarnai gambar tempat ibadah, menyanyikan lagu religi, atau menggambar cerita para nabi.',
                'Fisik-Motorik' => 'Manfaatkan seni untuk melatih motorik: menggambar gerakan besar (motorik kasar) dan membuat kerajinan detail (motorik halus).',
                'Kognitif' => 'Hubungkan seni dengan kognitif: menggambar pola berurutan, menyusun balok warna-warni, atau bermain musik sambil menghitung ketukan.',
                'Bahasa' => 'Kembangkan bahasa melalui seni: ajak mendeskripsikan gambarnya, bernyanyi dengan lirik, atau bercerita tentang karya seninya.',
                'Sosial-Emosional' => 'Gunakan seni kelompok untuk membangun sosial: melukis bersama, pertunjukan musik mini, atau proyek seni kolaboratif.',
            ],
            'Kognitif' => [
                'Agama & Moral' => 'Manfaatkan rasa ingin tahu untuk mengenalkan agama: eksplorasi alam (ciptaan Tuhan) dan percobaan sains sederhana.',
                'Fisik-Motorik' => 'Hubungkan kognitif dengan motorik: obstacle course dengan instruksi berurutan dan permainan strategi fisik.',
                'Bahasa' => 'Kembangkan bahasa melalui kognitif: ajak menjelaskan langkah eksperimen atau menceritakan proses pemecahan masalah.',
                'Sosial-Emosional' => 'Latih sosial melalui permainan kelompok yang membutuhkan kerjasama dan strategi bersama.',
                'Seni' => 'Hubungkan kognitif dengan seni: membuat pola, menggambar bentuk geometri, atau menyusun komposisi warna.',
            ],
            'Bahasa' => [
                'Agama & Moral' => 'Manfaatkan kekuatan bahasa untuk mengenalkan agama: mendongeng kisah nabi, menghafal doa pendek, dan bernyanyi lagu religi.',
                'Fisik-Motorik' => 'Hubungkan bahasa dengan motorik: bermain "Simon Says" atau instruksi gerakan menggunakan kalimat lengkap.',
                'Kognitif' => 'Kembangkan kognitif melalui bahasa: tebak-tebakan logika, bercerita dengan alur sebab-akibat, dan diskusi tentang benda sekitar.',
                'Sosial-Emosional' => 'Latih sosial melalui bermain peran dengan dialog, mengungkapkan perasaan dengan kata-kata, dan drama sederhana.',
                'Seni' => 'Hubungkan bahasa dengan seni: mendongeng kreatif, membuat puisi sederhana, dan bernyanyi lagu anak.',
            ],
            'Fisik-Motorik' => [
                'Agama & Moral' => 'Hubungkan motorik dengan agama: praktik gerakan ibadah, senam dengan lagu religi, dan kegiatan fisik bernilai moral.',
                'Kognitif' => 'Kembangkan kognitif melalui fisik: treasure hunt dengan petunjuk logika, atau olahraga yang membutuhkan strategi.',
                'Bahasa' => 'Latih bahasa melalui fisik: berikan instruksi verbal saat bermain, ajak menjelaskan gerakan yang dilakukan.',
                'Sosial-Emosional' => 'Kembangkan sosial melalui olahraga tim dan permainan kelompok yang membutuhkan kerjasama fisik.',
                'Seni' => 'Hubungkan motorik dengan seni: menari, senam kreasi, dan membuat karya seni tiga dimensi.',
            ],
            'Agama & Moral' => [
                'Fisik-Motorik' => 'Hubungkan agama dengan fisik: praktik gerakan ibadah dan jalan-jalan alam sambil bersyukur.',
                'Kognitif' => 'Kembangkan kognitif melalui diskusi tentang ciptaan Tuhan dan pelajaran moral dari cerita agama.',
                'Bahasa' => 'Latih bahasa melalui menghafal doa, bercerita kisah nabi, dan berdiskusi tentang perbuatan baik.',
                'Sosial-Emosional' => 'Hubungkan moral dengan sosial: kegiatan berbagi, tolong-menolong, dan bermain peran sikap terpuji.',
                'Seni' => 'Kembangkan seni melalui religi: mewarnai gambar masjid, bernyanyi lagu islami, membuat kaligrafi sederhana.',
            ],
            'Sosial-Emosional' => [
                'Agama & Moral' => 'Manfaatkan kepekaan sosial untuk mengenalkan agama: berbagi dengan teman dan membantu yang membutuhkan.',
                'Fisik-Motorik' => 'Latih motorik melalui permainan kelompok yang membutuhkan gerakan fisik dan kerjasama.',
                'Kognitif' => 'Kembangkan kognitif melalui diskusi kelompok, board games strategi, dan memecahkan masalah bersama.',
                'Bahasa' => 'Latih bahasa melalui kegiatan sosial: berdiskusi kelompok, bermain peran dengan dialog, dan presentasi di depan teman.',
                'Seni' => 'Hubungkan sosial dengan seni: proyek seni kolaboratif, pertunjukan bersama, dan karya seni berkelompok.',
            ],
        ];

        return $map[$k][$l] ?? "Manfaatkan ketertarikan anak pada {$k} sebagai jembatan untuk menstimulasi aspek {$l} yang masih perlu dikembangkan.";
    }
}
