<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use Carbon\Carbon;
use Faker\Factory as Faker;

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

        $siswaData = [];
        
        // Buat 20 siswa per kelas (diperlukan untuk 5 cluster yang distinct)
        foreach ($kelasIds as $kelasId) {
            for ($i = 0; $i < 20; $i++) {
                $gender = $faker->randomElement(['Laki-Laki', 'Perempuan']);
                $firstName = $faker->firstName($gender == 'Laki-Laki' ? 'male' : 'female');
                $lastName = $faker->lastName();
                $siswaData[] = [
                    'nama' => $firstName . ' ' . $lastName,
                    'nis' => $faker->unique()->numerify('2025#####'),
                    'jenis_kelamin' => $gender,
                    // 'tempat_lahir' => $faker->city, // Tidak ada di DB
                    'tanggal_lahir' => $faker->dateTimeBetween('-6 years', '-4 years')->format('Y-m-d'),
                    // 'alamat' => $faker->address, // Tidak ada di DB
                    // 'nama_ayah' => $faker->name('male'), // Tidak ada di DB
                    // 'nama_ibu' => $faker->name('female'), // Tidak ada di DB
                    // 'pekerjaan_ayah' => $faker->jobTitle, // Tidak ada di DB
                    // 'pekerjaan_ibu' => $faker->jobTitle, // Tidak ada di DB
                    // 'no_hp_ortu' => $faker->phoneNumber, // Tidak ada di DB
                    'kelas_id' => $kelasId,
                    // 'status' => 'Aktif', // Tidak ada di DB
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($siswaData, 50) as $chunk) {
            Siswa::insert($chunk);
        }
    }
}
