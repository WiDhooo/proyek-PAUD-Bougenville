<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\NilaiAbsensi;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function dashboard()
    {
        // Ambil jadwal beserta nama kelas dan guru-nya (biar tidak error kalau guru kosong)
        $jadwal = Jadwal::with(['kelas', 'guru'])->get();

        return view('guru.dashboard', compact('jadwal'));
    }

    public function dataSiswa()
    {
        // Ambil semua siswa tanpa filter guru/kelas
        $murid = Siswa::all();
        return view('guru.data_siswa', compact('murid'));
    }

    public function pilihKelas()
    {
        $guru = Guru::first(); // sementara ambil guru pertama
        $kelas = $guru->kelas; // ambil semua kelas guru ini
        return view('guru.pilih_kelas', compact('kelas'));
    }

    public function nilaiAbsensi(Kelas $kelas)
    {
        $murid = $kelas->siswa; // siswa hanya di kelas itu
        return view('guru.nilai_absensi', compact('murid', 'kelas'));
    }

    public function simpanNilaiAbsensi(Request $request, Kelas $kelas)
    {
        foreach ($kelas->siswa as $siswa) {
            NilaiAbsensi::updateOrCreate(
                ['siswa_id' => $siswa->id],
                [
                    'absensi' => $request->absensi[$siswa->id],
                    'nilai' => $request->nilai[$siswa->id],
                    'catatan' => $request->catatan[$siswa->id],
                ]
            );
        }
        return redirect()->route('guru.nilai_absensi.kelas', $kelas->id)
                        ->with('success', 'Data absensi dan nilai berhasil disimpan!');
    }

}
