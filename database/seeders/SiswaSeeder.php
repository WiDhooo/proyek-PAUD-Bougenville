<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Keuangan; // Tambahkan import model Keuangan
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB; // Tambahkan untuk transaksi database

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $now = Carbon::now();
        
        $kelasIds = Kelas::pluck('id')->toArray();
        
        if (empty($kelasIds)) {
            $this->command->warn('Tidak ada data Kelas. Pastikan KelasSeeder dijalankan terlebih dahulu.');
            return;
        }

        // Gunakan DB::transaction agar jika salah satu gagal, data tidak berantakan
        DB::transaction(function () use ($faker, $now, $kelasIds) {
            foreach ($kelasIds as $kelasId) {
                // Buat 20 siswa per kelas (diperlukan untuk 5 cluster yang distinct)
                for ($i = 0; $i < 20; $i++) {
                    $gender = $faker->randomElement(['Laki-Laki', 'Perempuan']);
                    $firstName = $faker->firstName($gender == 'Laki-Laki' ? 'male' : 'female');
                    $lastName = $faker->lastName();
                    
                    // 1. Buat Data Siswa
                    $siswa = Siswa::create([
                        'nama' => $firstName . ' ' . $lastName,
                        'nis' => $faker->unique()->numerify('2025#####'),
                        'jenis_kelamin' => $gender,
                        'tanggal_lahir' => $faker->dateTimeBetween('-6 years', '-4 years')->format('Y-m-d'),
                        'kelas_id' => $kelasId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // 2. Otomatis Buat Data Keuangan Kategori Pendaftaran untuk siswa tersebut
                    Keuangan::create([
                        'tanggal' => $now->format('Y-m-d'),
                        'kategori' => 'Pendaftaran',
                        'siswa_id' => $siswa->id, // Mengambil ID dari siswa yang baru saja dibuat
                        'jumlah' => 200000,
                        // Gunakan locale Indonesia agar tersimpan sebagai "Mei 2026"
                        'bulan_pembayaran' => $now->locale('id')->translatedFormat('F Y'),
                        'status' => 'Sudah Bayar',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });

        $this->command->info('Berhasil membuat data siswa beserta biaya pendaftarannya.');
    }
}