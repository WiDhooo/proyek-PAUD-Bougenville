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
            // Tambahkan seeder lain di sini jika ada
            ProfilSeeder::class, // PENTING: Memastikan data profil dibuat
            GuruSeeder::class,
            GaleriSeeder::class,
            // MuridSeeder::class,
            // KelasSeeder::class,
        ]);
    }
}
