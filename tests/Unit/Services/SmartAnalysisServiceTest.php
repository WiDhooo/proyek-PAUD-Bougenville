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
}
