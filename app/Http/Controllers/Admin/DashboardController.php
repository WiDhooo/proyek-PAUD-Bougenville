<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa; 
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total siswa (bukan murid)
        $totalSiswa = Siswa::count();

        // Hitung total guru
        $totalGuru = Guru::count();

        // Hitung total kelas
        $totalKelas = Kelas::count();

        // Jadwal dummy (kalau nanti mau buat dari database tinggal ganti)
        $jadwal = [
            'Senin' => [
                'Mandiri - A'   => 'Bu Rani',
                'Ceria - B'     => 'Pak Dedi',
                'Kreatif - A'   => 'Bu Ines',
            ],
            'Selasa' => [
                'Mandiri - A'   => 'Bu Rani',
                'Ceria - B'     => 'Pak Dedi',
                'Kreatif - A'   => 'Bu Ines',
            ],
            'Rabu' => [
                'Mandiri - A'   => null,
                'Ceria - B'     => 'Pak Dedi',
                'Kreatif - A'   => 'Bu Ines',
            ],
        ];

        return view('admin.dashboard.index', [
            'data' => [
                'total_murid' => $totalSiswa,
                'total_guru'  => $totalGuru,
                'total_kelas' => $totalKelas,
                'jadwal'      => $jadwal
            ]
        ]);
    }
}
