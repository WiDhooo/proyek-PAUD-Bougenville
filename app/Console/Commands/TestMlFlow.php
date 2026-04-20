<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\AspekPenilaian;
use App\Models\NilaiRapor;
use App\Services\MlRaporService;
use App\Models\HasilAnalisis;

class TestMlFlow extends Command
{
    protected $signature = 'rapor:test-ml';
    protected $description = 'Test the full ML flow with Feature Engineering (aggregated aspects)';

    public function handle(MlRaporService $mlService)
    {
        $this->info('=== RAPOR DIGITAL ML FLOW TEST (v2: Feature Engineering) ===');
        $this->newLine();

        // Step 0: Health Check
        $this->info('[Step 0] Checking ML Service health...');
        if ($mlService->healthCheck()) {
            $this->info('✅ ML Service is reachable.');
        } else {
            $this->error('❌ ML Service is NOT reachable. Run: cd python_ml && python3 app.py');
            return;
        }

        // Step 1: Ensure enough students
        $this->info('[Step 1] Preparing data...');
        $siswas = Siswa::limit(10)->get();

        if ($siswas->count() < 5) {
            $this->error('❌ Need at least 5 students. Run: php artisan migrate:fresh --seed');
            return;
        }
        $this->info("Found {$siswas->count()} students.");

        // Step 2: Seed grades
        $this->info('[Step 2] Seeding dummy grades (random 1-4)...');
        $aspeks = AspekPenilaian::all();

        if ($aspeks->isEmpty()) {
            $this->error('❌ No Aspek Penilaian found. Run: php artisan db:seed --class=AspekPenilaianSeeder');
            return;
        }

        // Give each student random grades
        foreach ($siswas as $siswa) {
            foreach ($aspeks as $aspek) {
                NilaiRapor::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'aspek_penilaian_id' => $aspek->id,
                        'periode' => 'Ganjil',
                        'tahun_ajaran' => '2025/2026',
                    ],
                    ['nilai' => rand(1, 4)]
                );
            }
        }

        // Step 3: FEATURE ENGINEERING — Aggregate to per-lingkup averages
        $this->info('[Step 3] Feature Engineering: Aggregating sub-indicators → 6 aspects...');
        $dataForMl = [];

        foreach ($siswas as $siswa) {
            $nilaiRapors = NilaiRapor::where('siswa_id', $siswa->id)
                ->where('periode', 'Ganjil')
                ->where('tahun_ajaran', '2025/2026')
                ->with('aspekPenilaian')
                ->get();

            $nilaiPerLingkup = $nilaiRapors
                ->groupBy(fn ($nr) => $nr->aspekPenilaian->lingkup ?? 'Lainnya')
                ->map(fn ($group) => round($group->avg('nilai'), 2))
                ->toArray();

            if (!empty($nilaiPerLingkup)) {
                $dataForMl[] = [
                    'siswa_id' => $siswa->id,
                    'nilai' => $nilaiPerLingkup, // {"Agama & Moral": 3.33, ...}
                ];
                $this->line("  → {$siswa->nama}: " . json_encode($nilaiPerLingkup));
            }
        }

        // Step 4: Call ML Service
        $this->newLine();
        $this->info('[Step 4] Sending aggregated data to Python ML Service...');
        $response = $mlService->analyzeData($dataForMl);

        if ($response['status'] === 'success') {
            $this->info('✅ Clustering successful!');
            $this->line("   Optimal K     : {$response['optimal_k']}");
            $this->line("   Silhouette    : {$response['silhouette_score']}");
            $this->newLine();

            // Show cluster assignments
            $this->info('[Step 5] Cluster Assignments:');
            foreach ($response['clusters'] as $siswaId => $clusterId) {
                $siswa = Siswa::find($siswaId);
                $this->line("  → {$siswa->nama} → Cluster {$clusterId}");
            }

            // Show AUTO-PROFILES
            $this->newLine();
            $this->info('[Step 6] Cluster Profiles (Auto-Profiling):');
            foreach ($response['profiles'] as $clusterId => $profile) {
                $this->newLine();
                $this->line("  📊 Cluster {$clusterId} ({$profile['jumlah_siswa']} siswa):");
                $this->line("     Aspek Dominan  : {$profile['aspek_dominan']}");
                $this->line("     Aspek Terendah : {$profile['aspek_terendah']}");
                $this->line("     Rata-rata Aspek:");
                foreach ($profile['rata_rata_aspek'] as $aspek => $avg) {
                    $bar = str_repeat('█', (int) round($avg * 5));
                    $this->line("       {$aspek}: {$avg}/4 {$bar}");
                }
            }

            // Save results
            $this->newLine();
            $this->info('[Step 7] Saving results to database...');
            foreach ($response['clusters'] as $siswaId => $clusterId) {
                HasilAnalisis::updateOrCreate(
                    ['siswa_id' => $siswaId, 'periode' => 'Ganjil', 'tahun_ajaran' => '2025/2026'],
                    ['cluster_group' => (string) $clusterId, 'raw_response' => $response]
                );
            }

            $this->newLine();
            $this->info('🎉 TEST PASSED! Full flow with Feature Engineering working correctly.');
        } elseif ($response['status'] === 'validation_error') {
            $this->error("⚠️ Guard Rails rejection: {$response['message']}");
        } else {
            $this->error("❌ ML error: {$response['message']}");
        }
    }
}
