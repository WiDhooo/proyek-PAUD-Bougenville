<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cukup ambil semua model.
        // Laravel akan otomatis menerapkan $casts dan accessors ('usia')
        // saat data ini diubah menjadi JSON di view.
        $siswa = Siswa::all();
        
        // Langsung kirim koleksi Model ke view.
        // HAPUS SEMUA BLOK .map()
        return view('admin.siswa.index', compact('siswa'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Tambahkan 'numeric' dan 'unique:siswa,nis'
            'nis' => 'required|numeric|unique:siswa,nis', 
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|string|max:15',
            // Tambahkan 'before:-2 years'
            'tanggal_lahir' => 'required|date|before:-2 years', 
        ], [
            'nis.numeric' => 'NIS harus berupa angka.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'tanggal_lahir.before' => 'Usia siswa minimal harus 2 tahun.',
        ]);

        Siswa::create($data);
        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Data Siswa berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $data = $request->validate([
            // Unique tapi abaikan ID siswa yang sedang diedit
            'nis' => ['required', 'numeric', Rule::unique('siswa', 'nis')->ignore($siswa->id)],
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|string|max:15',
            'tanggal_lahir' => 'required|date|before:-2 years',
        ], [
            'nis.numeric' => 'NIS harus berupa angka.',
            'nis.unique' => 'NIS sudah digunakan siswa lain.',
            'tanggal_lahir.before' => 'Usia siswa minimal harus 2 tahun.',
        ]);

        $siswa->update($data);
        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Data siswa berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Tambahkan ini agar fungsi hapus berjalan
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        
        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Data siswa berhasil dihapus!');
    }
}