<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\AspekPenilaian;
use App\Models\NilaiRapor;
use Carbon\Carbon;

class NilaiRaporSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        
        // Ambil beberapa siswa (misal 5 siswa pertama per kelas)
        $siswas = Siswa::take(15)->get();
        $aspeks = AspekPenilaian::all();

        if ($siswas->isEmpty() || $aspeks->isEmpty()) {
            return;
        }

        $nilaiData = [];

        foreach ($siswas as $siswa) {
            foreach ($aspeks as $aspek) {
                // Random nilai 1-4, tapi dibobolkan ke nilai bagus (3 atau 4) agar terlihat normal
                // 10% chance nilai 1, 20% nilai 2, 40% nilai 3, 30% nilai 4
                $rand = rand(1, 100);
                if ($rand <= 10) $nilai = 1;
                elseif ($rand <= 30) $nilai = 2;
                elseif ($rand <= 70) $nilai = 3;
                else $nilai = 4;

                $nilaiData[] = [
                    'siswa_id' => $siswa->id,
                    'aspek_penilaian_id' => $aspek->id,
                    'nilai' => $nilai,
                    'periode' => 'Ganjil',
                    'tahun_ajaran' => '2025/2026',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert chunked
        foreach (array_chunk($nilaiData, 100) as $chunk) {
            NilaiRapor::insert($chunk);
        }
    }
}
