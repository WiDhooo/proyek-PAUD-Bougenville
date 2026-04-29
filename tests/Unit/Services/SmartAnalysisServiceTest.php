<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SmartAnalysisService;

/**
 * Unit test untuk SmartAnalysisService — pure function tanpa DB dependency.
 * Test logic rule-based analisis minat bakat anak PAUD.
 *
 * Signature: analyze(iterable $nilaiPerLingkup, iterable $nilaiRapors, ?array $clusterProfile = null): array
 *
 * Return structure:
 * [
 *   'label_utama'      => string,
 *   'aspek_kuat'       => array (sorted desc),
 *   'aspek_lemah'      => array (sorted asc),
 *   'deskripsi'        => string,
 *   'saran_kekuatan'   => array,
 *   'saran_kelemahan'  => array,
 *   'saran_integratif' => string|array,
 *   'red_flags'        => array,
 *   'cluster_profile'  => array|null,
 * ]
 */
class SmartAnalysisServiceTest extends TestCase
{
    private SmartAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SmartAnalysisService();
    }

    /**
     * Helper: create a mock NilaiRapor object for red flag testing.
     * $nilaiRapors expects iterable of objects with ->nilai, ->aspekPenilaian->indikator, etc.
     */
    private function makeMockNilaiRapor(int $nilai, string $indikator = 'Test', string $subLingkup = 'Sub', string $lingkup = 'Kognitif'): object
    {
        return (object) [
            'nilai' => $nilai,
            'aspekPenilaian' => (object) [
                'indikator' => $indikator,
                'sub_lingkup' => $subLingkup,
                'lingkup' => $lingkup,
            ],
        ];
    }

    // ================================================================
    // Test: analyze() — Happy Path
    // ================================================================

    public function test_analyze_returns_expected_structure(): void
    {
        $nilaiPerLingkup = [
            'Agama & Moral' => 3.5,
            'Fisik-Motorik' => 2.5,
            'Kognitif' => 3.8,
            'Bahasa' => 3.0,
            'Sosial-Emosional' => 2.0,
            'Seni' => 3.2,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('label_utama', $result);
        $this->assertArrayHasKey('aspek_kuat', $result);
        $this->assertArrayHasKey('aspek_lemah', $result);
        $this->assertArrayHasKey('deskripsi', $result);
        $this->assertArrayHasKey('saran_kekuatan', $result);
        $this->assertArrayHasKey('saran_kelemahan', $result);
        $this->assertArrayHasKey('saran_integratif', $result);
        $this->assertArrayHasKey('red_flags', $result);
        $this->assertArrayHasKey('cluster_profile', $result);
    }

    public function test_analyze_high_scores_detects_aspek_kuat(): void
    {
        $nilaiPerLingkup = [
            'Agama & Moral' => 4.0,
            'Fisik-Motorik' => 3.8,
            'Kognitif' => 3.9,
            'Bahasa' => 3.7,
            'Sosial-Emosional' => 3.6,
            'Seni' => 3.5,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        // Semua >= THRESHOLD_KUAT (3.5), jadi semua harus masuk aspek_kuat
        $this->assertNotEmpty($result['aspek_kuat']);
        $this->assertEmpty($result['aspek_lemah']);
        $this->assertEmpty($result['red_flags']);
        $this->assertNotEquals('Berkembang Merata (Generalis)', $result['label_utama']);
    }

    public function test_analyze_low_scores_detects_aspek_lemah(): void
    {
        $nilaiPerLingkup = [
            'Agama & Moral' => 1.5,
            'Fisik-Motorik' => 1.8,
            'Kognitif' => 1.2,
            'Bahasa' => 1.0,
            'Sosial-Emosional' => 1.5,
            'Seni' => 1.3,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        // Semua < THRESHOLD_LEMAH (2.5), jadi semua harus masuk aspek_lemah
        $this->assertNotEmpty($result['aspek_lemah']);
        $this->assertEmpty($result['aspek_kuat']);
    }

    // ================================================================
    // Test: Red Flag Detection
    // ================================================================

    public function test_analyze_detects_red_flags_on_score_1(): void
    {
        $nilaiPerLingkup = [
            'Agama & Moral' => 2.0,
            'Kognitif' => 3.0,
        ];

        // Buat mock nilaiRapors dengan skor = 1 (RED_FLAG_SCORE)
        $nilaiRapors = [
            $this->makeMockNilaiRapor(1, 'Menghafal Surat', 'Hafalan', 'Agama & Moral'),
            $this->makeMockNilaiRapor(3, 'Berhitung', 'Numerik', 'Kognitif'),
        ];

        $result = $this->service->analyze($nilaiPerLingkup, $nilaiRapors);

        // Harus ada 1 red flag (skor = 1)
        $this->assertCount(1, $result['red_flags']);
        $this->assertEquals('Menghafal Surat', $result['red_flags'][0]['indikator']);
    }

    public function test_analyze_no_red_flags_when_all_scores_above_1(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.0];

        $nilaiRapors = [
            $this->makeMockNilaiRapor(2, 'Berhitung', 'Numerik', 'Kognitif'),
            $this->makeMockNilaiRapor(3, 'Logika', 'Penalaran', 'Kognitif'),
        ];

        $result = $this->service->analyze($nilaiPerLingkup, $nilaiRapors);

        $this->assertEmpty($result['red_flags']);
    }

    // ================================================================
    // Test: Edge Cases
    // ================================================================

    public function test_analyze_with_empty_nilaiPerLingkup(): void
    {
        $result = $this->service->analyze([], []);

        $this->assertIsArray($result);
        $this->assertEmpty($result['aspek_kuat']);
        $this->assertEmpty($result['aspek_lemah']);
        $this->assertEmpty($result['red_flags']);
        $this->assertEquals('Berkembang Merata (Generalis)', $result['label_utama']);
    }

    public function test_analyze_with_mixed_scores(): void
    {
        $nilaiPerLingkup = [
            'Kognitif' => 4.0,         // Kuat
            'Seni' => 3.8,             // Kuat
            'Bahasa' => 3.0,           // Tengah (bukan kuat/lemah)
            'Sosial-Emosional' => 1.5,  // Lemah
        ];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        $this->assertCount(3, $result['aspek_kuat']); // Kognitif, Seni, Bahasa (>= THRESHOLD_KUAT 3.0)
        $this->assertCount(1, $result['aspek_lemah']); // Sosial-Emosional
        $this->assertArrayHasKey('Kognitif', $result['aspek_kuat']);
        $this->assertArrayHasKey('Sosial-Emosional', $result['aspek_lemah']);
    }

    public function test_analyze_aspek_kuat_sorted_descending(): void
    {
        $nilaiPerLingkup = [
            'Bahasa' => 3.5,
            'Kognitif' => 4.0,
            'Seni' => 3.7,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        $keys = array_keys($result['aspek_kuat']);
        // Kognitif (4.0) harus di urutan pertama
        $this->assertEquals('Kognitif', $keys[0]);
    }

    // ================================================================
    // Test: Cluster Profile Integration
    // ================================================================

    public function test_analyze_passes_cluster_profile_through(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.5];
        $clusterProfile = ['cluster' => 0, 'dominant' => 'Visual-Spatial'];

        $result = $this->service->analyze($nilaiPerLingkup, [], $clusterProfile);

        $this->assertEquals($clusterProfile, $result['cluster_profile']);
    }

    public function test_analyze_cluster_profile_null_when_not_provided(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.0];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        $this->assertNull($result['cluster_profile']);
    }

    // ================================================================
    // Test: Peer Comparison
    // ================================================================

    public function test_peer_comparison_returns_above_when_selisih_besar(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.8, 'Bahasa' => 2.0];
        $clusterProfile = [
            'rata_rata_aspek' => ['Kognitif' => 2.5, 'Bahasa' => 2.0],
            'aspek_dominan'   => 'Kognitif',
            'aspek_terendah'  => 'Bahasa',
            'cohesion_score'  => 0.8,
            'jumlah_siswa'    => 5,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, [], $clusterProfile);

        $this->assertArrayHasKey('peer_comparison', $result);
        $this->assertNotNull($result['peer_comparison']);
        // Kognitif selisih 3.8 - 2.5 = 1.3 > 0.3 → above
        $detail = $result['peer_comparison']['detail'];
        $this->assertEquals('above', $detail['Kognitif']['status']);
        $this->assertEquals('Di atas', $detail['Kognitif']['label']);
    }

    public function test_peer_comparison_returns_below_when_selisih_negatif_besar(): void
    {
        $nilaiPerLingkup = ['Seni' => 1.5];
        $clusterProfile = [
            'rata_rata_aspek' => ['Seni' => 3.0],
            'aspek_dominan'   => 'Seni',
            'aspek_terendah'  => 'Seni',
            'cohesion_score'  => 0.7,
            'jumlah_siswa'    => 4,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, [], $clusterProfile);

        $detail = $result['peer_comparison']['detail'];
        $this->assertEquals('below', $detail['Seni']['status']);
        $this->assertEquals('Di bawah', $detail['Seni']['label']);
    }

    public function test_peer_comparison_returns_equal_when_selisih_kecil(): void
    {
        $nilaiPerLingkup = ['Bahasa' => 3.0];
        $clusterProfile = [
            'rata_rata_aspek' => ['Bahasa' => 3.1],
            'aspek_dominan'   => 'Bahasa',
            'aspek_terendah'  => 'Bahasa',
            'cohesion_score'  => 0.9,
            'jumlah_siswa'    => 6,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, [], $clusterProfile);

        // Selisih 3.0 - 3.1 = -0.1, dalam batas ±0.3
        $detail = $result['peer_comparison']['detail'];
        $this->assertEquals('equal', $detail['Bahasa']['status']);
        $this->assertEquals('Setara', $detail['Bahasa']['label']);
    }

    public function test_peer_comparison_label_has_no_embedded_numbers(): void
    {
        // Pastikan label tidak mengandung angka (selisih bukan bagian dari label)
        $nilaiPerLingkup = ['Kognitif' => 3.9];
        $clusterProfile = [
            'rata_rata_aspek' => ['Kognitif' => 2.0],
            'aspek_dominan'   => 'Kognitif',
            'aspek_terendah'  => 'Kognitif',
            'cohesion_score'  => 0.6,
            'jumlah_siswa'    => 4,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, [], $clusterProfile);

        // Label harus plain text, tidak mengandung angka selisih
        $detail = $result['peer_comparison']['detail'];
        $this->assertArrayHasKey('Kognitif', $detail);
        $label = $detail['Kognitif']['label'];
        $this->assertMatchesRegularExpression('/^(Di atas|Di bawah|Setara)$/', $label);
    }

    public function test_peer_comparison_empty_when_no_cluster_profile(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.5];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        $this->assertArrayHasKey('peer_comparison', $result);
        $this->assertEmpty($result['peer_comparison']);
    }

    // ================================================================
    // Test: Saran Integratif & Generalis
    // ================================================================

    public function test_saran_integratif_returned_when_kuat_dan_lemah_ada(): void
    {
        $nilaiPerLingkup = [
            'Kognitif'        => 4.0,  // Kuat
            'Seni'            => 1.5,  // Lemah
            'Bahasa'          => 3.0,
            'Agama & Moral'   => 3.0,
            'Fisik-Motorik'   => 3.0,
            'Sosial-Emosional'=> 3.0,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        // Harus ada saran integratif karena ada aspek kuat dan lemah
        $this->assertNotEmpty($result['saran_integratif']);
        $this->assertIsString($result['saran_integratif']);
    }

    public function test_saran_generalis_returned_when_semua_nilai_tengah(): void
    {
        // Semua nilai di antara THRESHOLD_LEMAH(2.0) dan THRESHOLD_KUAT(3.0), eksklusif
        $nilaiPerLingkup = [
            'Agama & Moral'    => 2.1,
            'Fisik-Motorik'    => 2.3,
            'Kognitif'         => 2.5,
            'Bahasa'           => 2.4,
            'Sosial-Emosional' => 2.6,
            'Seni'             => 2.8,
        ];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        $this->assertEmpty($result['aspek_kuat']);
        $this->assertEmpty($result['aspek_lemah']);
        $this->assertEquals('Berkembang Merata (Generalis)', $result['label_utama']);
        // Saran generalis harus ada
        $this->assertNotEmpty($result['saran_generalis']);
    }

    // ================================================================
    // Test: Trend Analysis (via previousSemesterNilai param)
    // ================================================================

    public function test_trend_analysis_naik_signifikan(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.8];
        $previousNilai   = ['Kognitif' => 2.5]; // delta = +1.3 → up_significant

        $result = $this->service->analyze($nilaiPerLingkup, [], null, $previousNilai);

        $this->assertArrayHasKey('trend_data', $result);
        $this->assertNotNull($result['trend_data']);
        $this->assertArrayHasKey('Kognitif', $result['trend_data']);
        $this->assertEquals('up_significant', $result['trend_data']['Kognitif']['trend']);
        $this->assertEquals('Naik signifikan', $result['trend_data']['Kognitif']['label']);
        // Pastikan label tidak mengandung ikon/simbol
        $this->assertDoesNotMatchRegularExpression('/[↑↓→]/', $result['trend_data']['Kognitif']['label']);
    }

    public function test_trend_analysis_stabil(): void
    {
        $nilaiPerLingkup = ['Bahasa' => 3.0];
        $previousNilai   = ['Bahasa' => 3.0]; // delta = 0 → stable

        $result = $this->service->analyze($nilaiPerLingkup, [], null, $previousNilai);

        $this->assertEquals('stable', $result['trend_data']['Bahasa']['trend']);
        $this->assertEquals('Stabil', $result['trend_data']['Bahasa']['label']);
    }

    public function test_trend_analysis_turun_signifikan(): void
    {
        $nilaiPerLingkup = ['Seni' => 1.5];
        $previousNilai   = ['Seni' => 3.5]; // delta = -2.0 → down_significant

        $result = $this->service->analyze($nilaiPerLingkup, [], null, $previousNilai);

        $this->assertEquals('down_significant', $result['trend_data']['Seni']['trend']);
        $this->assertEquals('Turun signifikan', $result['trend_data']['Seni']['label']);
    }

    public function test_trend_data_empty_when_no_previous(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.5];

        $result = $this->service->analyze($nilaiPerLingkup, []);

        // Ketika tidak ada previousSemester, trend_data harus null atau empty
        $this->assertTrue(
            is_null($result['trend_data']) || empty($result['trend_data']),
            'trend_data should be null or empty when no previous semester is provided'
        );
    }

    public function test_trend_label_has_no_icon_symbols(): void
    {
        $nilaiPerLingkup = ['Kognitif' => 3.8];
        $previousNilai   = ['Kognitif' => 2.0]; // up_significant

        $result = $this->service->analyze($nilaiPerLingkup, [], null, $previousNilai);

        foreach ($result['trend_data'] as $trend) {
            // Label tidak boleh mengandung karakter ikon
            $this->assertDoesNotMatchRegularExpression('/[↑↓→⬆⬇]/', $trend['label']);
        }
    }
}
