<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    public function index() {
        $data = Ebook::latest()->get();
        return view('admin.ebook.index', compact('data'));
    }

    public function store(Request $request) {
        $request->validate([
            'judul' => 'required|string|max:255',
            'file_gambar.*' => 'required|image|mimes:jpeg,png,jpg',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $finalPages = [];
        $totalSize = 0;

        // 1. Simpan Gambar Materi
        if ($request->hasFile('file_gambar')) {
            foreach ($request->file('file_gambar') as $index => $file) {
                $path = $file->store('ebooks/content', 'public');
                $totalSize += $file->getSize();
                // Inisialisasi index dimulai dari 1 agar memudahkan mapping user
                $finalPages[$index + 1] = [
                    'image' => $path,
                    'audio' => null
                ];
            }
        }

        // 2. Mapping Audio ke Halaman (jika ada)
        if ($request->has('audio_page')) {
            foreach ($request->audio_page as $key => $targetPage) {
                if (isset($request->file('audio_file')[$key]) && isset($finalPages[$targetPage])) {
                    $audioFile = $request->file('audio_file')[$key];
                    $audioPath = $audioFile->store('ebooks/audio', 'public');
                    $totalSize += $audioFile->getSize();
                    $finalPages[$targetPage]['audio'] = $audioPath;
                }
            }
        }

        // 3. Simpan Thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('ebooks/thumbnails', 'public');
            $totalSize += $request->file('thumbnail')->getSize();
        }

        ksort($finalPages);

        Ebook::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => array_values($finalPages), // Simpan sebagai JSON array
            'thumbnail' => $thumbnailPath,
            'ukuran_file' => $totalSize,
        ]);

        return back()->with('success', 'E-Book berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $ebook = Ebook::findOrFail($id);
        
        $request->validate([
            'judul' => 'required|string|max:255',
        ]);

        $tempPages = [];

        // 1. PROSES HALAMAN LAMA (Bisa pindah urutan, ganti suara, atau terhapus)
        if ($request->has('existing_pages')) {
            foreach ($request->existing_pages as $index => $data) {
                $order = (int) $data['order'];
                $image = $data['image'];
                $audio = $data['old_audio']; // Gunakan suara lama sebagai default

                // Cek jika ada upload suara baru untuk baris ini
                if ($request->hasFile("update_audio.$index")) {
                    if ($audio) Storage::disk('public')->delete($audio); // Hapus file suara lama
                    $audio = $request->file("update_audio.$index")->store('ebooks/audio', 'public');
                }

                // Jika admin klik "Hapus Suara" di UI, variabel p.audio di Alpine menjadi null
                // maka $data['old_audio'] akan kosong. Kita tangani di sini:
                if (empty($audio) && !empty($data['old_audio']) && !$request->hasFile("update_audio.$index")) {
                    Storage::disk('public')->delete($data['old_audio']);
                    $audio = null;
                }

                // Simpan sementara dengan key order agar bisa diurutkan
                // Gunakan $index sebagai desimal agar jika nomor order sama, tidak saling tindih
                $tempPages[$order . '.' . $index] = [
                    'image' => $image,
                    'audio' => $audio
                ];
            }
        }

        // 2. URUTKAN BERDASARKAN INPUT NOMOR HALAMAN
        ksort($tempPages);
        $finalPages = array_values($tempPages);

        // 3. TAMBAH HALAMAN BARU (Jika ada)
        if ($request->hasFile('add_new_pages')) {
            foreach ($request->file('add_new_pages') as $file) {
                $path = $file->store('ebooks/content', 'public');
                $finalPages[] = ['image' => $path, 'audio' => null];
            }
        }

        // 4. UPDATE THUMBNAIL
        $thumb = $ebook->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumb) Storage::disk('public')->delete($thumb);
            $thumb = $request->file('thumbnail')->store('ebooks/thumbnails', 'public');
        }

        // 5. SIMPAN KE DATABASE
        $ebook->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $finalPages,
            'thumbnail' => $thumb,
        ]);

        return back()->with('success', 'E-Book diperbarui dengan urutan baru.');
    }

    public function destroy($id)
    {
        $ebook = Ebook::findOrFail($id);
        if ($ebook->thumbnail) Storage::disk('public')->delete($ebook->thumbnail);
        
        $pages = $ebook->file_path;
        foreach ($pages as $p) {
            Storage::disk('public')->delete($p['image']);
            if(!empty($p['audio'])) Storage::disk('public')->delete($p['audio']);
        }

        $ebook->delete();
        return back()->with('success', 'E-Book berhasil dihapus.');
    }

    // Fungsi tambahan untuk tampilan user
    public function show($id) {
        $ebook = Ebook::findOrFail($id);
        return view('ebook.show', compact('ebook'));
    }

    public function read($id) {
        $ebook = Ebook::findOrFail($id);
        $pages = $ebook->file_path;
        return view('ebook.read', compact('ebook', 'pages'));
    }
}