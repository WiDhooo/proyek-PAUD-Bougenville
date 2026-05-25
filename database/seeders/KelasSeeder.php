<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Guru;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        
        // Ambil guru yang ada (pastikan GuruSeeder sudah dijalankan)
        $gurus = Guru::limit(3)->get();
        
        if ($gurus->count() < 1) {
            $this->command->warn('Tidak ada data Guru. Pastikan GuruSeeder dijalankan terlebih dahulu.');
            return;
        }

        $kelasData = [
            [
                'nama_kelas' => 'TK A (Matahari)',
                'kelas' => 'TK A',
                'guru_id' => $gurus[0]->id, // Guru 1
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => 'TK B (Bulan)',
                'kelas' => 'TK B',
                'guru_id' => $gurus[1]->id ?? $gurus[0]->id, // Guru 2
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        Kelas::insert($kelasData);
    }
}
