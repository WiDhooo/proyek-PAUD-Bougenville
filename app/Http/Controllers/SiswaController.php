<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $murid = Siswa::all();
        
        // Langsung kirim koleksi Model ke view.
        // HAPUS SEMUA BLOK .map()
        return view('admin.murid.index', compact('murid'));
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
        // PERBAIKAN VALIDASI:
        // 1. Tambahkan 'unique:siswa' untuk NIS
        // 2. Gunakan 'date' untuk tanggal_lahir
        $data = $request->validate([
            'nis' => 'required|string|max:100|unique:siswa,nis',
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|string|max:15',
            'tanggal_lahir' => 'required|date',
        ]);

        Siswa::create($data);
        return redirect()->route('admin.murid.index')
                         ->with('success', 'Data Murid berhasil disimpan!');
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
        $murid = Siswa::findOrFail($id);

        // PERBAIKAN VALIDASI:
        // 1. Tambahkan 'unique' tapi abaikan ID saat ini
        // 2. Gunakan 'date'
        $data = $request->validate([
            'nis' => 'required|string|max:100|unique:siswa,nis,' . $murid->id,
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|string|max:15',
            'tanggal_lahir' => 'required|date',
        ]);

        $murid->update($data);
        return redirect()->route('admin.murid.index')
                         ->with('success', 'Data murid berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Tambahkan ini agar fungsi hapus berjalan
        $murid = Siswa::findOrFail($id);
        $murid->delete();
        
        return redirect()->route('admin.murid.index')
                         ->with('success', 'Data murid berhasil dihapus!');
    }
}