<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;

class KelasController extends Controller
{
    public function index()
    {
        $semuaKelas = Kelas::with('guru')->get();
        $kelas = $semuaKelas->map(function ($item) {
        return [
            'id' => $item->id,
            'nama_kelas' => $item->nama_kelas,
            'kelas' => $item->kelas,
            'guru_id' => $item->guru_id,
            'wali' => $item->guru ? $item->guru->nama : 'Belum Diatur'
            ];
        });
        $gurus = Guru::all();
        return view('admin.kelas.index', compact('kelas', 'gurus'));
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function show($id)
    {
        $kelas = Kelas::with('siswa')->findOrFail($id);
        $siswa_di_kelas = $kelas->siswa;
        $semua_siswa = Siswa::whereNull('kelas_id')->get();
        return view('admin.kelas.show', [
            'kelas' => $kelas,
            'siswa_di_kelas' => $siswa_di_kelas,
            'semua_siswa' => $semua_siswa
        ]);
    }

    public function assignSiswa(Request $request, $id)
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);
        $siswaIds = $request->input('siswa_ids');
        Siswa::whereIn('id', $siswaIds)->update(['kelas_id' => $id]);
        return redirect()->route('admin.kelas.show', $id)
                         ->with('success', 'Siswa berhasil ditambahkan ke kelas!');
    }

    public function unassignSiswa($id, $siswaId)
    {
        $siswa = Siswa::where('id', $siswaId)->where('kelas_id', $id)->firstOrFail();
        $siswa->update(['kelas_id' => null]);
        return redirect()->route('admin.kelas.show', $id)
                         ->with('success', 'Siswa berhasil dihapus dari kelas!');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'kelas' => 'required|string|max:1',
            'guru_id' => 'required'
        ],
        [
            'nama_kelas.required' => 'Nama Kelas wajib diisi.',
            'nama_kelas.string' => 'Nama Kelas harus berupa karakter.',
            'nama_kelas.max' => 'Nama Kelas maksimal 100 karakter',
            'kelas.required' => 'Kelas wajib diisi.',
            'kelas.string' => 'Kelas harus berupa karakter.',
            'kelas.max' => 'Kelas maksimal 1 karakter'
        ]);

        Kelas::create($data);
        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Data kelas berhasil disimpan!');
    
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $data = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'kelas' => 'required|string|max:1',
            'guru_id' => 'required'
        ],
        [
            'nama_kelas.required' => 'Nama Kelas wajib diisi.',
            'nama_kelas.string' => 'Nama Kelas harus berupa karakter.',
            'nama_kelas.max' => 'Nama Kelas maksimal 100 karakter',
            'kelas.required' => 'Kelas wajib diisi.',
            'kelas.string' => 'Kelas harus berupa karakter.',
            'kelas.max' => 'Kelas maksimal 1 karakter'
        ]);

        $kelas -> update($data);
        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Data kelas berhasil diubah!');
    
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        
        // Set kelas_id menjadi NULL untuk semua siswa di kelas ini
        Siswa::where('kelas_id', $id)->update(['kelas_id' => null]);
        
        // Baru hapus kelasnya
        $kelas->delete();
        
        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Data kelas berhasil dihapus dan siswa dipindahkan ke tanpa kelas!');
    }

}
