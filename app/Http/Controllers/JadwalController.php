<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;

class JadwalController extends Controller
{
    public function dashboard()
    {
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();
        
        // Ambil jadwal dari database
        $jadwals = Jadwal::with(['guru', 'kelas'])->get();
        $kelasList = Kelas::all();
        $gurus = Guru::all();
        
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalData = [];
        
        foreach ($hari as $h) {
            $jadwalData[$h] = [];
            foreach ($kelasList as $kelas) {
                $jadwalItem = $jadwals->where('hari', $h)
                                      ->where('kelas_id', $kelas->id)
                                      ->first();
                
                $jadwalData[$h][$kelas->nama_kelas . ' - ' . $kelas->kelas] = 
                    $jadwalItem ? $jadwalItem->guru->nama : '-';
            }
        }
        
        $data = [
            'total_siswa' => $totalSiswa,
            'total_guru' => $totalGuru,
            'total_kelas' => $totalKelas,
            'jadwal' => $jadwalData
        ];

        return view('admin.dashboard', compact('data', 'jadwals', 'kelasList', 'gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ], [
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        // Cek apakah sudah ada jadwal untuk kelas dan hari yang sama
        $exists = Jadwal::where('kelas_id', $request->kelas_id)
                        ->where('hari', $request->hari)
                        ->exists();

        if ($exists) {
            return back()->with('error', 'Jadwal untuk kelas ini pada hari tersebut sudah ada!');
        }

        Jadwal::create($request->all());
        
        return redirect()->route('admin.dashboard')
                        ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ], [
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        $jadwal->update($request->all());
        
        return redirect()->route('admin.dashboard')
                        ->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        
        return redirect()->route('admin.dashboard')
                        ->with('success', 'Jadwal berhasil dihapus!');
    }

    public function destroyAll()
    {
        try {
            $count = Jadwal::count();
            Jadwal::truncate();
            
            return redirect()->route('admin.dashboard')
                            ->with('success', "Berhasil menghapus {$count} jadwal!");
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                            ->with('error', 'Gagal menghapus jadwal!');
        }
    }
}