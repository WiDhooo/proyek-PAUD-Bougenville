<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Pastikan import model User
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Password default untuk semua akun: 'password123'
        $defaultPassword = Hash::make('password123');
        $now = Carbon::now();

        $users = [
            // --- 1. AKUN ADMIN ---
            [
                'name'              => 'Administrator',
                'email'             => 'admin@sekolah.com',
                'password'          => $defaultPassword,
                'role'              => 'admin', // Sesuai ENUM di migration
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],

            // --- 2. AKUN GURU (Dari Data PPT) ---
            // Saya buatkan email dummy menggunakan nama depan mereka
            [
                'name'              => 'Endang Sulistiawati',
                'email'             => 'endang@sekolah.com',
                'password'          => $defaultPassword,
                'role'              => 'guru',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Wiwin Charyani',
                'email'             => 'wiwin@sekolah.com',
                'password'          => $defaultPassword,
                'role'              => 'guru',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Ecin Kuraesin',
                'email'             => 'ecin@sekolah.com',
                'password'          => $defaultPassword,
                'role'              => 'guru',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Sukarsih',
                'email'             => 'sukarsih@sekolah.com',
                'password'          => $defaultPassword,
                'role'              => 'guru',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Kowiyah',
                'email'             => 'kowiyah@sekolah.com',
                'password'          => $defaultPassword,
                'role'              => 'guru',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Yeany Marlitha',
                'email'             => 'yeany@sekolah.com',
                'password'          => $defaultPassword,
                'role'              => 'guru',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        // Masukkan data ke tabel users
        User::insert($users);
    }
}