<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;   
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Halaman index kelas.
     */
    public function index()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('admin.kelas.index', compact('kelas'));
    }

    /**
     * Store kelas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kelas'      => 'required|string|max:255',
            'wali'       => 'required|string|max:255',
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'kelas'      => $request->kelas,
            'wali'       => $request->wali,
        ]);

        return back()->with('success', 'Kelas berhasil dibuat!');
    }

    /**
     * Show detail kelas + murid.
     */
    public function show($id)
    {
        $kelas = Kelas::findOrFail($id);

        // List murid yang sedang berada di kelas
        $murid_di_kelas = Siswa::where('kelas_id', $id)->get();

        // Semua murid yang BELUM punya kelas
        $semua_murid = Siswa::whereNull('kelas_id')->get();

        return view('admin.kelas.show', compact('kelas', 'murid_di_kelas', 'semua_murid'));
    }

    /**
     * Update kelas.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'kelas'      => 'required',
            'wali'       => 'required',
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->only(['nama_kelas', 'kelas', 'wali']));

        return back()->with('success', 'Data kelas berhasil diperbarui!');
    }

    /**
     * Hapus kelas.
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        // Unassign murid dari kelas sebelum delete
        Siswa::where('kelas_id', $id)->update(['kelas_id' => null]);

        // Hapus kelas
        $kelas->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Tambah murid ke kelas.
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'murid_ids' => 'required|array'
        ]);

        Siswa::whereIn('id', $request->murid_ids)->update([
            'kelas_id' => $id
        ]);

        return back()->with('success', 'Murid berhasil ditambahkan ke kelas!');
    }

    /**
     * Hapus murid dari kelas tertentu.
     */
    public function unassignMurid($kelas_id, $murid_id)
    {
        $murid = Siswa::findOrFail($murid_id);

        if ($murid->kelas_id == $kelas_id) {
            $murid->kelas_id = null;
            $murid->save();
        }

        return back()->with('success', 'Murid berhasil dikeluarkan dari kelas.');
    }
}
