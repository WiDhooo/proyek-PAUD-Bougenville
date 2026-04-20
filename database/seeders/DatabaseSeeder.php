<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Panggil seeder yang baru dibuat
        $this->call([
            // Urutan PENTING karena ada relasi (Foreign Keys)
            UserSeeder::class,           // 1. User dulu
            ProfilSeeder::class,         // 2. Profil
            GuruSeeder::class,           // 3. Guru (butuh User)
            AspekPenilaianSeeder::class, // 4. Master Data Penilaian
            MasterRekomendasiSeeder::class, // 5. Master Data Rekomendasi
            KelasSeeder::class,          // 6. Kelas (butuh Guru)
            SiswaSeeder::class,          // 7. Siswa (butuh Kelas)
            JadwalSeeder::class,         // 8. Jadwal (butuh Kelas & Guru)
            GaleriSeeder::class,         // 9. Galeri
            NilaiRaporSeeder::class,     // 10. Nilai (butuh Siswa & Aspek)
        ]);
    }
}
