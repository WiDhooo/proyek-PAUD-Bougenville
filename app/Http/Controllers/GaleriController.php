<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Galeri;
use Illuminate\Support\Str;

class GaleriController extends Controller
{

    public function kegiatan()
    {
        $galeris = Galeri::latest()->get();
        return view('kegiatan', compact('galeris'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galeris = Galeri::all();
        return view('admin.profil.galeri', compact('galeris'));
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
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'gambar' => 'required|file',
        ]);

        if ($request->hasFile('gambar')) {
            $judul = $request->judul;
            $slug = Str::slug($judul);

            // 3. Buat nama file unik: slug + timestamp + ekstensi
            $imageName = $slug . '-' . time() . '.' . $request->gambar->extension();

            // 4. Simpan file
            $request->gambar->move(public_path('images/galeri'), $imageName);
            
            // 5. Masukkan nama file ke array data untuk disimpan ke DB
            $data['gambar'] = $imageName;
        }

        Galeri::create($data);
        return redirect()->route('admin.galeri.index')
                         ->with('success', 'Kegiatan baru berhasil ditambahkan!');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $galeris = Galeri::findOrFail($id);
        $galeris -> delete();
        return redirect()->route('admin.galeri.index')
                        ->with('success', 'Data kegiatan berhasil dihapus!');
    }
}
