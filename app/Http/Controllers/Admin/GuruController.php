<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::orderBy('nama')->get();

        return view('admin.guru.index', compact('guru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => 'required',
            'jabatan'    => 'required',
            'alamat'     => 'nullable',
            'pendidikan' => 'nullable',
        ]);

        Guru::create($validated);

        return back()->with('success', 'Guru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $guru->update([
            'nama'       => $request->nama,
            'jabatan'    => $request->jabatan,
            'alamat'     => $request->alamat,
            'pendidikan' => $request->pendidikan,
        ]);

        return back()->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Guru::destroy($id);

        return back()->with('success', 'Guru berhasil dihapus!');
    }
}
