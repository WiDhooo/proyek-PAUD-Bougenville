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
        
        $jadwals = Jadwal::with(['guru', 'kelas'])->get();
        $kelasList = Kelas::all();
        $gurus = Guru::all();
        
        // Struktur data untuk tabel jadwal
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalData = [];
        
        foreach ($hari as $h) {
            $jadwalData[$h] = [];
            foreach ($kelasList as $kelas) {
                // Ambil jadwal spesifik cell ini
                $jadwalItem = $jadwals->where('hari', $h)
                                      ->where('kelas_id', $kelas->id)
                                      ->sortBy('waktu_mulai'); // Sort biar rapi jika ada >1 mapel
                
                // Format tampilan di cell tabel
                if ($jadwalItem->isNotEmpty()) {
                    $cellContent = [];
                    foreach($jadwalItem as $item) {
                        $start = date('H:i', strtotime($item->waktu_mulai));
                        $end = date('H:i', strtotime($item->waktu_selesai));
                        // Format: "Nama Guru (08:00 - 09:00)"
                        $cellContent[] = [
                            'guru' => $item->guru->nama,
                            'waktu' => "$start - $end",
                            'data' => $item // Simpan data asli untuk tombol edit/hapus
                        ];
                    }
                    $jadwalData[$h][$kelas->nama_kelas . ' - ' . $kelas->kelas] = $cellContent;
                } else {
                    $jadwalData[$h][$kelas->nama_kelas . ' - ' . $kelas->kelas] = [];
                }
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
        $this->validateRequest($request);

        // 1. Cek Bentrok GURU (Guru tidak bisa di 2 tempat)
        if ($this->checkGuruClash($request)) {
            return back()->with('error', 'Gagal! Guru tersebut sedang mengajar di kelas lain pada jam tersebut.');
        }

        // 2. Cek Bentrok KELAS (Kelas tidak bisa dipakai 2 mapel bersamaan)
        if ($this->checkKelasClash($request)) {
            return back()->with('error', 'Gagal! Kelas tersebut sudah terisi jadwal lain pada jam tersebut.');
        }

        Jadwal::create($request->all());
        
        return redirect()->route('admin.dashboard')
                        ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $this->validateRequest($request);

        // Cek Bentrok (Exclude ID sendiri agar tidak error saat update diri sendiri)
        if ($this->checkGuruClash($request, $id)) {
            return back()->with('error', 'Gagal update! Guru bentrok dengan jadwal lain.');
        }

        if ($this->checkKelasClash($request, $id)) {
            return back()->with('error', 'Gagal update! Kelas bentrok dengan jadwal lain.');
        }

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
        Jadwal::truncate();
        return redirect()->route('admin.dashboard')
                        ->with('success', 'Semua jadwal berhasil dihapus!');
    }

    // --- HELPER FUNCTIONS ---

    private function validateRequest($request) {
        return $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus lebih akhir dari waktu mulai.'
        ]);
    }

    // Logika Irisan Waktu: (StartA < EndB) && (EndA > StartB)
    private function checkGuruClash($request, $ignoreId = null) {
        $query = Jadwal::where('hari', $request->hari)
                       ->where('guru_id', $request->guru_id)
                       ->where('waktu_mulai', '<', $request->waktu_selesai)
                       ->where('waktu_selesai', '>', $request->waktu_mulai);
        
        if ($ignoreId) $query->where('id', '!=', $ignoreId);
        
        return $query->exists();
    }

    private function checkKelasClash($request, $ignoreId = null) {
        $query = Jadwal::where('hari', $request->hari)
                       ->where('kelas_id', $request->kelas_id)
                       ->where('waktu_mulai', '<', $request->waktu_selesai)
                       ->where('waktu_selesai', '>', $request->waktu_mulai);
                       
        if ($ignoreId) $query->where('id', '!=', $ignoreId);
        
        return $query->exists();
    }
}