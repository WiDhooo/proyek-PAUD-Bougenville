<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\MlRaporService;
use Illuminate\Support\Facades\Http;

/**
 * Unit test untuk MlRaporService — mock HTTP ke Python ML Service.
 * Test retry mechanism, error handling, dan health check.
 */
class MlRaporServiceTest extends TestCase
{
    private MlRaporService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MlRaporService();
    }

    // ================================================================
    // Test: analyzeData() — Happy Path
    // ================================================================

    public function test_analyze_data_returns_success_on_valid_response(): void
    {
        Http::fake([
            '*/analyze' => Http::response([
                'status' => 'success',
                'optimal_k' => 3,
                'silhouette_score' => 0.45,
                'clusters' => [
                    '1' => ['cluster' => 0, 'profile' => ['dominant' => 'Kognitif']],
                    '2' => ['cluster' => 1, 'profile' => ['dominant' => 'Seni']],
                ],
            ], 200),
        ]);

        $data = [
            ['siswa_id' => 1, 'nilai' => ['Kognitif' => 3.5, 'Seni' => 2.0]],
            ['siswa_id' => 2, 'nilai' => ['Kognitif' => 2.0, 'Seni' => 3.8]],
        ];

        $result = $this->service->analyzeData($data);

        $this->assertEquals('success', $result['status']);
        $this->assertEquals(3, $result['optimal_k']);
        $this->assertArrayHasKey('clusters', $result);
    }

    // ================================================================
    // Test: analyzeData() — Error Paths
    // ================================================================

    public function test_analyze_data_returns_error_on_422_validation(): void
    {
        Http::fake([
            '*/analyze' => Http::response([
                'status' => 'error',
                'message' => 'Minimal 2 siswa diperlukan.',
            ], 422),
        ]);

        $data = [
            ['siswa_id' => 1, 'nilai' => ['Kognitif' => 3.5]],
        ];

        $result = $this->service->analyzeData($data);

        $this->assertEquals('error', $result['status']);
        // 422 responses are treated as server-side failure by MlRaporService
        $this->assertNotEmpty($result['message']);
    }

    public function test_analyze_data_returns_error_on_500(): void
    {
        Http::fake([
            '*/analyze' => Http::response([
                'status' => 'error',
                'message' => 'Internal server error.',
            ], 500),
        ]);

        $data = [
            ['siswa_id' => 1, 'nilai' => ['Kognitif' => 3.5, 'Seni' => 2.0]],
            ['siswa_id' => 2, 'nilai' => ['Kognitif' => 2.0, 'Seni' => 3.8]],
        ];

        $result = $this->service->analyzeData($data);

        $this->assertEquals('error', $result['status']);
    }

    public function test_analyze_data_handles_connection_timeout(): void
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
        });

        $data = [
            ['siswa_id' => 1, 'nilai' => ['Kognitif' => 3.5, 'Seni' => 2.0]],
            ['siswa_id' => 2, 'nilai' => ['Kognitif' => 2.0, 'Seni' => 3.8]],
        ];

        $result = $this->service->analyzeData($data);

        $this->assertEquals('error', $result['status']);
        // Connection errors produce the generic "Gagal menghubungi" message
        $this->assertStringContainsString('Gagal', $result['message']);
    }

    // ================================================================
    // Test: healthCheck()
    // ================================================================

    public function test_health_check_returns_true_on_200(): void
    {
        Http::fake([
            '*/health' => Http::response(['status' => 'ok'], 200),
        ]);

        $this->assertTrue($this->service->healthCheck());
    }

    public function test_health_check_returns_false_on_failure(): void
    {
        Http::fake([
            '*/health' => Http::response(null, 503),
        ]);

        $this->assertFalse($this->service->healthCheck());
    }

    public function test_health_check_returns_false_on_connection_error(): void
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('refused');
        });

        $this->assertFalse($this->service->healthCheck());
    }
}
