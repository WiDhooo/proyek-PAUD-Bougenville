<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Password default untuk semua akun guru
        $defaultPassword = Hash::make('password');
        $now = Carbon::now();

        $dataGuru = [
            // 1. KEPALA SEKOLAH
            [
                'nama'          => 'Endang Sulistiawati',
                'username'      => 'endang', // Username login
                'password'      => $defaultPassword,
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1973-03-05', // 5 Maret 1973
                'no_hp'         => '081513747681',
                'alamat'        => 'Jl. Kebon Kelapa Rt 06/ Rw 010 No. 8 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Kepala Sekolah', // Sesuai ENUM di migration
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 2. SEKRETARIS
            [
                'nama'          => 'Wiwin Charyani',
                'username'      => 'wiwin',
                'password'      => $defaultPassword,
                'tempat_lahir'  => 'Bekasi',
                'tanggal_lahir' => '1974-07-02', // 2 Juli 1974
                'no_hp'         => '085882611604',
                'alamat'        => 'Jl. Kebon Kelapa Tinggi Rt 07/ Rw 08 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Sekretaris',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 3. BENDAHARA
            [
                'nama'          => 'Ecin Kuraesin',
                'username'      => 'ecin',
                'password'      => $defaultPassword,
                'tempat_lahir'  => 'Bogor',
                'tanggal_lahir' => '1966-07-27', // 27 Juli 1966
                'no_hp'         => '082123507303',
                'alamat'        => 'Jl. Kebon Kelapa Rt 08/ Rw 010 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Bendahara',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 4. PENDIDIK 1
            [
                'nama'          => 'Sukarsih',
                'username'      => 'sukarsih',
                'password'      => $defaultPassword,
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1966-05-16', // 16 Mei 1966
                'no_hp'         => '081513747681', // Sesuai data di PPT (sama dengan Bu Endang, mungkin perlu dicek realnya)
                'alamat'        => 'Jl. Kebon Kelapa Rt 06/ Rw 010 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Pendidik',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 5. PENDIDIK 2
            [
                'nama'          => 'Kowiyah',
                'username'      => 'kowiyah',
                'password'      => $defaultPassword,
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1963-02-07', // 7 Februari 1963
                'no_hp'         => '081316334526',
                'alamat'        => 'Jl. Kebon Kelapa Tinggi Rt 017/ Rw 08 Kel. Utan Kayu Selatan Matraman Jakarta Timur',
                'jabatan'       => 'Pendidik',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // 6. PENDIDIK 3
            [
                'nama'          => 'Yeany Marlitha',
                'username'      => 'yeany',
                'password'      => $defaultPassword,
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1966-01-08', // 8 Januari 1966
                'no_hp'         => '087780755522',
                'alamat'        => 'Jl. Kebon Manggis Rt 02/ Rw 03 Kel. Kebon Manggis Matraman Jakarta Timur',
                'jabatan'       => 'Pendidik',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        // Insert data ke database
        Guru::insert($dataGuru);
    }
}