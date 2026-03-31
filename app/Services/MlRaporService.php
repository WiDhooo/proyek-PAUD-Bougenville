<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlRaporService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.ml.url', 'http://127.0.0.1:5001');
        $this->apiKey = config('services.ml.api_key', '');
    }

    /**
     * Send student grades to Python ML Service for clustering analysis.
     *
     * @param array $data Structure: [['siswa_id' => 1, 'nilai' => [4,3,2,...]], ...]
     * @return array Structured response with 'status' key
     */
    public function analyzeData(array $data): array
    {
        $url = $this->baseUrl . '/analyze';

        try {
            $request = Http::timeout(15)->retry(2, 500);

            // [S-7] Kirim API key jika dikonfigurasi
            if (!empty($this->apiKey)) {
                $request = $request->withHeaders(['X-API-Key' => $this->apiKey]);
            }

            $response = $request->post($url, ['data' => $data]);

            if ($response->successful()) {
                return $response->json();
            }

            // Handle 422 (Guard Rails rejection from Python)
            if ($response->status() === 422) {
                $body = $response->json();
                Log::warning('ML Service Validation Error', $body);
                return [
                    'status' => 'validation_error',
                    'message' => $body['message'] ?? 'Data ditolak oleh layanan analisis.',
                ];
            }

            // Handle other errors
            Log::error('ML Service Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [
                'status' => 'error',
                'message' => 'Layanan analisis mengembalikan error.',
            ];

        } catch (\Exception $e) {
            Log::error('ML Service Connection Failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Gagal menghubungi layanan AI. Pastikan server Python berjalan di ' . $this->baseUrl,
            ];
        }
    }

    /**
     * Check if the ML service is reachable.
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
