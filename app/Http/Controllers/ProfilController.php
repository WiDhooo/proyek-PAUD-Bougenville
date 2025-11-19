<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profil; // Pastikan import model Profil
use App\Models\VisiMisi; // Pastikan import model VisiMisi

class ProfilController extends Controller
{

    public function tentang()
    {
        $profil = Profil::first();
        
        if (!$profil) {
            return view('tentang', [
                'tentang_sekolah' => 'Informasi belum tersedia.',
                'visi' => collect([]),
                'misi' => collect([])
            ]);
        }

        $visi = $profil->visiMisi()->where('tipe', 'visi')->get();
        $misi = $profil->visiMisi()->where('tipe', 'misi')->get();

        return view('tentang', compact('profil', 'visi', 'misi'));
    }
    
    /**
     * Display a listing of the resource.
     * Metode ini biasanya untuk menampilkan daftar atau ringkasan.
     */
    public function index()
    {
        // Ambil profil sekolah pertama (asumsi ID=1 atau yang pertama)
        $profil = Profil::first(); 

        // Ambil Misi sekolah
        $misi = $profil->visiMisi()->where('tipe', 'misi')->get();

        // Ambil Visi sekolah
        $visi = $profil->visiMisi()->where('tipe', 'visi')->get();

        // Di sini seharusnya mengarah ke view index/ringkasan, 
        // tetapi kita akan mengarahkan ke halaman edit untuk demo
        // return view('admin.profil.index', compact('profil', 'visi', 'misi'));
        
        // Untuk contoh ini, saya akan buat dummy index view
        return view('admin.profil.index', compact('profil', 'visi', 'misi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Cari Profil berdasarkan ID
        $profil = Profil::findOrFail($id);

        // Ambil Misi sekolah secara terpisah (tanpa orderBy sesuai permintaan)
        $misi = $profil->visiMisi()->where('tipe', 'misi')->get();

        // Ambil Visi sekolah secara terpisah (tanpa orderBy sesuai permintaan)
        $visi = $profil->visiMisi()->where('tipe', 'visi')->get();

        // Kirim data ke view admin.profil.edit
        return view('admin.profil.edit', compact('profil', 'visi', 'misi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. VALIDASI DATA
        $request->validate([
            'tentang_sekolah' => 'nullable|string',
            // Validasi menggunakan field 'isi' dari form
            'vision' => 'nullable|array', 
            'mission' => 'nullable|array',
            'vision.*.isi' => 'required|string|max:1000', // Validasi setiap poin Visi
            'mission.*.isi' => 'required|string|max:1000', // Validasi setiap poin Misi
        ]);

        // Cari Profil
        $profil = Profil::findOrFail($id);

        // 2. UPDATE TABEL PROFIL (TENTANG SEKOLAH)
        $profil->tentang_sekolah = $request->tentang_sekolah;
        $profil->save();
        
        // 3. UPDATE TABEL VISI MISI (ONE-TO-MANY)
        
        // Hapus SEMUA poin Visi dan Misi lama yang terkait dengan profil ini
        // Ini adalah cara termudah untuk menyinkronkan data dinamis
        $profil->visiMisi()->delete(); 

        // Proses dan Simpan Poin VISI BARU
        if ($request->has('vision')) {
            foreach ($request->vision as $poin) {
                // Pastikan menggunakan $poin['isi'] karena itu yang dikirim dari form
                if (!empty($poin['isi'])) { 
                    $profil->visiMisi()->create([
                        'tipe' => 'visi',
                        'isi' => $poin['isi'], // Menggunakan kolom 'isi'
                    ]);
                }
            }
        }

        // Proses dan Simpan Poin MISI BARU
        if ($request->has('mission')) {
            foreach ($request->mission as $poin) {
                // Pastikan menggunakan $poin['isi'] karena itu yang dikirim dari form
                if (!empty($poin['isi'])) {
                    $profil->visiMisi()->create([
                        'tipe' => 'misi',
                        'isi' => $poin['isi'], // Menggunakan kolom 'isi'
                    ]);
                }
            }
        }

        // Redirect kembali ke halaman edit dengan pesan sukses
        return redirect()->route('admin.profil.edit', $profil->id)->with('success', 'Profil Sekolah berhasil diperbarui!');
    }
    
}