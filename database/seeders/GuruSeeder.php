<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guru;
use Carbon\Carbon;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $dataGuru = [
            // 1. KEPALA SEKOLAH (User ID = 2)
            [
                'user_id'       => 2, // Ubah dari 1 jadi 2
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1973-03-05',
                'no_hp'         => '081513747681',
                'alamat'        => 'Jl. Kebon Kelapa Rt 06/ Rw 010 No. 8 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Kepala Sekolah',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 2. SEKRETARIS (User ID = 3)
            [
                'user_id'       => 3, // Ubah dari 2 jadi 3
                'tempat_lahir'  => 'Bekasi',
                'tanggal_lahir' => '1974-07-02',
                'no_hp'         => '085882611604',
                'alamat'        => 'Jl. Kebon Kelapa Tinggi Rt 07/ Rw 08 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Sekretaris',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 3. BENDAHARA (User ID = 4)
            [
                'user_id'       => 4, // Ubah dari 3 jadi 4
                'tempat_lahir'  => 'Bogor',
                'tanggal_lahir' => '1966-07-27',
                'no_hp'         => '082123507303',
                'alamat'        => 'Jl. Kebon Kelapa Rt 08/ Rw 010 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Bendahara',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 4. PENDIDIK 1 (User ID = 5)
            [
                'user_id'       => 5, // Ubah dari 4 jadi 5
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1966-05-16',
                'no_hp'         => '081513747681',
                'alamat'        => 'Jl. Kebon Kelapa Rt 06/ Rw 010 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Pendidik',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 5. PENDIDIK 2 (User ID = 6)
            [
                'user_id'       => 6, // Ubah dari 5 jadi 6
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1963-02-07',
                'no_hp'         => '081316334526',
                'alamat'        => 'Jl. Kebon Kelapa Tinggi Rt 017/ Rw 08 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Pendidik',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 6. PENDIDIK 3 (User ID = 7)
            [
                'user_id'       => 7, // Ubah dari 6 jadi 7
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1966-01-08',
                'no_hp'         => '087780755522',
                'alamat'        => 'Jl. Kebon Manggis Rt 02/ Rw 03 Kel. Kebon Manggis Matraman Jakarta Timur',
                'jabatan'       => 'Pendidik',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        Guru::insert($dataGuru);
    }
}