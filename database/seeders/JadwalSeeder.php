<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $kelas = Kelas::all();

        if ($kelas->isEmpty()) {
            return;
        }

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalData = [];

        foreach ($kelas as $k) {
            foreach ($hari as $h) {
                $jadwalData[] = [
                    'kelas_id' => $k->id,
                    'guru_id' => $k->guru_id, // Guru kelas mengajar jadwal ini
                    'hari' => $h,
                    'waktu_mulai' => '08:00:00',
                    'waktu_selesai' => '11:00:00',
                    'kegiatan' => 'Kegiatan Belajar Mengajar (Tema Mingguan)', // Asumsi ada kolom kegiatan atau sejenisnya
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        // Cek dulu kolom apa saja yang ada di tabel jadwal, karena di model tadi tidak terlihat 'kegiatan'
        // Tapi biasanya jadwal butuh keterangan. Jika gagal, nanti kita sesuaikan.
        // Berdasarkan model sebelumnya: fillable = ['guru_id', 'kelas_id', 'hari', 'waktu_mulai', 'waktu_selesai']
        // Jadi kita hapus 'kegiatan' agar aman.
        
        $cleanData = array_map(function($item) {
            unset($item['kegiatan']);
            return $item;
        }, $jadwalData);

        Jadwal::insert($cleanData);
    }
}
