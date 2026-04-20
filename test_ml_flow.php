<?php

use App\Models\Siswa;
use App\Models\AspekPenilaian;
use App\Models\NilaiRapor;
use App\Services\MlRaporService;
use App\Models\HasilAnalisis;

// 1. Create Dummy Student and Grades
$siswa = Siswa::first();
if (!$siswa) {
    echo "Error: No siswa found. Please create a siswa first.\n";
    exit;
}

echo "Using Siswa: {$siswa->nama} (ID: {$siswa->id})\n";

$aspekPenilaian = AspekPenilaian::all();
$dataNilai = [];

echo "Seeding dummy grades...\n";
foreach ($aspekPenilaian as $aspek) {
    $nilai = rand(1, 4);
    NilaiRapor::updateOrCreate(
        [
            'siswa_id' => $siswa->id,
            'aspek_penilaian_id' => $aspek->id,
            'periode' => 'Ganjil',
            'tahun_ajaran' => '2025/2026'
        ],
        ['nilai' => $nilai]
    );
    $dataNilai[] = $nilai;
}

// Prepare data for ML Service
$payload = [
    [
        'siswa_id' => $siswa->id,
        'nilai' => $dataNilai
    ]
];

// 2. Call ML Service
echo "Calling ML Service...\n";
$mlService = new MlRaporService();
$response = $mlService->analyzeData($payload);

if ($response) {
    echo "ML Service Response Received:\n";
    print_r($response);

    // 3. Verify Database Storage
    if (isset($response['results'][$siswa->id])) {
        $clusterId = $response['results'][$siswa->id];
        
        HasilAnalisis::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'periode' => 'Ganjil',
                'tahun_ajaran' => '2025/2026',
            ],
            [
                'cluster_group' => (string) $clusterId,
                'raw_response' => $response
            ]
        );
        
        echo "Success! HasilAnalisis saved to database.\n";
    } else {
        echo "Error: Cluster ID not found in response.\n";
    }
} else {
    echo "Error: Failed to connect to ML Service.\n";
}
