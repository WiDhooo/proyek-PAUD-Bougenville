<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Illuminate\Http\Request;
use App\Models\Siswa;

class KeuanganController extends Controller
{
    public function index()
    {
        // Ambil data keuangan beserta data siswanya
        $data = Keuangan::with('siswa')->latest()->get();
        // Ambil semua siswa untuk dropdown di form
        $siswas = Siswa::all(); 

        return view('admin.keuangan.index', compact('data', 'siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'siswa_id' => 'required|exists:siswa,id',
            'bulan_pembayaran' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
        ]);

        Keuangan::create([
            'tanggal' => $request->tanggal,
            'jenis' => 'pemasukan',
            'siswa_id' => $request->siswa_id,
            'bulan_pembayaran' => $request->bulan_pembayaran,
            'jumlah' => $request->jumlah,
        ]);

        return back()->with('success', 'Pembayaran siswa berhasil dicatat.');
    }

    public function destroy($id)
    {
        Keuangan::findOrFail($id)->delete();
        return back()->with('success', 'Data keuangan berhasil dihapus.');
    }
}
