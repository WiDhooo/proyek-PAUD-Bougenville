<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Galeri; 

class GaleriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data array dari SQL sebelumnya
        $data = [
            [
                'id' => 2,
                'judul' => 'Makan Siang Bersama',
                'deskripsi' => 'Suasana makan siang yang menyenangkan, di mana anak-anak belajar makan mandiri bersama teman-teman.',
                'gambar' => 'makan-siang-bersama-1763538125.png',
                'created_at' => '2025-11-19 00:42:05',
                'updated_at' => '2025-11-19 00:42:05',
            ],
            [
                'id' => 3,
                'judul' => 'Melatih Kreativitas Anak',
                'deskripsi' => 'Anak-anak diajak mengekspresikan ide dan imajinasi melalui berbagai kegiatan seni seperti membuat kerajinan.',
                'gambar' => 'melatih-kreativitas-anak-1763538158.png',
                'created_at' => '2025-11-19 00:42:38',
                'updated_at' => '2025-11-19 00:42:38',
            ],
            [
                'id' => 4,
                'judul' => 'Latihan Marching Band',
                'deskripsi' => 'Melatih koordinasi, motorik, serta kekompakan anak melalui latihan musik marching band dan gerak ritmis.',
                'gambar' => 'latihan-marching-band-1763538170.png',
                'created_at' => '2025-11-19 00:42:50',
                'updated_at' => '2025-11-19 00:42:50',
            ],
            [
                'id' => 5,
                'judul' => 'Sarapan Bersama Teman',
                'deskripsi' => 'Menumbuhkan kebiasaan hidup sehat serta kemampuan sosial anak melalui kegiatan sarapan bersama teman-teman.',
                'gambar' => 'sarapan-bersama-teman-1763538187.jpg',
                'created_at' => '2025-11-19 00:43:07',
                'updated_at' => '2025-11-19 00:43:07',
            ],
            [
                'id' => 6,
                'judul' => 'Persiapan Mengikuti Pawai',
                'deskripsi' => 'Foto bersama sebelum melakukan pawai, anak-anak belajar bekerja sama dan menyiapkan atribut pawai.',
                'gambar' => 'persiapan-mengikuti-pawai-1763538200.jpg',
                'created_at' => '2025-11-19 00:43:20',
                'updated_at' => '2025-11-19 00:43:20',
            ],
            [
                'id' => 7,
                'judul' => 'Kegiatan Olahraga Bersama',
                'deskripsi' => 'Kegiatan olahraga yang dilakukan setiap hari jumat untuk memperkuat tubuh, dan rasa kebersamaan.',
                'gambar' => 'kegiatan-olahraga-bersama-1763538222.jpg',
                'created_at' => '2025-11-19 00:43:42',
                'updated_at' => '2025-11-19 00:43:42',
            ],
        ];

        // 2. Gunakan Method insert() milik Model
        // Ini lebih cepat daripada create() untuk data banyak (bulk insert)
        Galeri::insert($data);
    }
}