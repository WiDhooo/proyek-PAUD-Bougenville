<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\NilaiAbsensi;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Request;

class GuruController extends Controller
{
    public function dashboard()
    {
        // Ambil jadwal beserta nama kelas dan guru-nya (biar tidak error kalau guru kosong)
        $jadwal = Jadwal::with(['kelas', 'guru'])->get();

        return view('guru.dashboard', compact('jadwal'));
    }

    public function dataSiswa()
    {
        // Ambil semua siswa tanpa filter guru/kelas
        $murid = Siswa::all();
        return view('guru.data_siswa', compact('murid'));
    }

    public function pilihKelas()
    {
        $guru = Guru::first(); // sementara ambil guru pertama
        $kelas = $guru->kelas; // ambil semua kelas guru ini
        return view('guru.pilih_kelas', compact('kelas'));
    }

    public function nilaiAbsensi(Kelas $kelas)
    {
        $murid = $kelas->siswa; // siswa hanya di kelas itu
        return view('guru.nilai_absensi', compact('murid', 'kelas'));
    }

    public function simpanNilaiAbsensi(Request $request, Kelas $kelas)
    {
        foreach ($kelas->siswa as $siswa) {
            NilaiAbsensi::updateOrCreate(
                ['siswa_id' => $siswa->id],
                [
                    'absensi' => $request->absensi[$siswa->id],
                    'nilai' => $request->nilai[$siswa->id],
                    'catatan' => $request->catatan[$siswa->id],
                ]
            );
        }
        return redirect()->route('guru.nilai_absensi.kelas', $kelas->id)
                        ->with('success', 'Data absensi dan nilai berhasil disimpan!');
    }

    public function index()
    {
        $gurus = Guru::all();
        return view('admin.guru.index', compact('gurus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:guru'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:20']
        ]);

        Guru::create($data);
        return redirect()->route('admin.guru.index')
                         ->with('success', 'Data Guru berhasil disimpan!');
    }

    public function update(Request $request, $id)
{
        $gurus = Guru::findOrFail($id);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            
            // 1. PERBAIKAN VALIDASI UNIQUE:
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('guru')->ignore($gurus->id) // <-- 'ignore' ID milik sendiri
            ],

            // 2. PERBAIKAN VALIDASI PASSWORD:
            // Dibuat 'nullable' (boleh kosong). Jika diisi, baru 'min:8' dan 'confirmed' dicek.
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'], // Dihapus max:255 karena di form <textarea>
            'jabatan' => [
                'required',
                'string',
                Rule::in(['Kepala Sekolah', 'Sekretaris', 'Bendahara', 'Pendidik']) // <-- Lebih aman pakai Rule::in
            ]
        ]);

        // 3. LOGIKA UPDATE PASSWORD:
        // Hanya update password jika field 'password' di form diisi (tidak kosong)
        if (!empty($data['password'])) {
            // Asumsi Anda punya Mutator (otomatis hash) di Model Guru.
            $gurus->password = $data['password'];
        }

        // 4. UPDATE DATA SELAIN PASSWORD:
        // 'except' digunakan agar password lama tidak tertimpa data kosong
        $gurus->update($request->except('password'));

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil diperbarui!'); // Pesan diganti
    }

    public function destroy(string $id)
    {
        // Tambahkan ini agar fungsi hapus berjalan
        $gurus = Guru::findOrFail($id);
        $gurus->delete();
        
        return redirect()->route('admin.guru.index')
                         ->with('success', 'Data guru berhasil dihapus!');
    }

}
