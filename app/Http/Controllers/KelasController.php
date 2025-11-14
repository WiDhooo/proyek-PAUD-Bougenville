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
        $murid_di_kelas = $kelas->siswa;
        $semua_murid = Siswa::whereNull('kelas_id')->get();
        return view('admin.kelas.show', [
            'kelas' => $kelas,
            'murid_di_kelas' => $murid_di_kelas,
            'semua_murid' => $semua_murid
        ]);
    }

    public function assignMurid(Request $request, $id)
    {
        $request->validate([
            'murid_ids' => 'required|array',
            'murid_ids.*' => 'exists:siswa,id',
        ]);
        $muridIds = $request->input('murid_ids');
        Siswa::whereIn('id', $muridIds)->update(['kelas_id' => $id]);
        return redirect()->route('admin.kelas.show', $id)
                         ->with('success', 'Murid berhasil ditambahkan ke kelas!');
    }

    public function unassignMurid($id, $muridId)
    {
        $murid = Siswa::where('id', $muridId)->where('kelas_id', $id)->firstOrFail();
        $murid->update(['kelas_id' => null]);
        return redirect()->route('admin.kelas.show', $id)
                         ->with('success', 'Murid berhasil dihapus dari kelas!');
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
        $kelas -> delete();
        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Data kelas berhasil dihapus!');
    }

}
