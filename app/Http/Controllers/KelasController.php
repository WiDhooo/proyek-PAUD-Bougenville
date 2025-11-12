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

}
