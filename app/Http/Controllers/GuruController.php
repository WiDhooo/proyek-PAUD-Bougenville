<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\NilaiAbsensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function dashboard()
    {
        // Ambil guru yang sedang login
        $guruId = Auth::id(); // atau Auth::user()->id
        
        // Ambil jadwal guru yang login beserta relasi kelas
        $jadwal = Jadwal::with('kelas')
                       ->where('guru_id', $guruId)
                       ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
                       ->get();

        return view('guru.dashboard', compact('jadwal'));
    }

    public function dataSiswa()
    {
        // Ambil guru yang sedang login
        $guruId = Auth::id();
        
        // Ambil kelas yang diajar oleh guru ini
        $kelasIds = Jadwal::where('guru_id', $guruId)
                          ->pluck('kelas_id')
                          ->unique();
        
        // Ambil siswa dari kelas-kelas tersebut
        $siswa = Siswa::whereIn('kelas_id', $kelasIds)->get();
        
        return view('guru.data_siswa', compact('siswa'));
    }

    public function pilihKelas()
    {
        // Ambil guru yang sedang login
        $guruId = Auth::id();
        
        // Ambil kelas yang diajar oleh guru ini (dari jadwal)
        $kelasIds = Jadwal::where('guru_id', $guruId)
                          ->pluck('kelas_id')
                          ->unique();
        
        $kelas = Kelas::whereIn('id', $kelasIds)->get();
        
        return view('guru.pilih_kelas', compact('kelas'));
    }

    public function nilaiAbsensi($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $murid = $kelas->siswa; // Ambil siswa dari kelas
        
        return view('guru.nilai_absensi', compact('murid', 'kelas'));
    }

    public function simpanNilaiAbsensi(Request $request, $kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        
        foreach ($kelas->siswa as $siswa) {
            NilaiAbsensi::updateOrCreate(
                ['siswa_id' => $siswa->id],
                [
                    'absensi' => $request->absensi[$siswa->id] ?? 'h',
                    'nilai' => $request->nilai[$siswa->id] ?? null,
                    'catatan' => $request->catatan[$siswa->id] ?? null,
                ]
            );
        }
        
        return redirect()->route('guru.nilai_absensi.kelas', $kelas->id)
                        ->with('success', 'Data absensi dan nilai berhasil disimpan!');
    }

    // ... method admin tetap sama ...
    
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
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('guru')->ignore($gurus->id)
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'jabatan' => [
                'required',
                'string',
                Rule::in(['Kepala Sekolah', 'Sekretaris', 'Bendahara', 'Pendidik'])
            ]
        ]);

        if (!empty($data['password'])) {
            $gurus->password = $data['password'];
        }

        $gurus->update($request->except('password'));

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $gurus = Guru::findOrFail($id);
        $gurus->delete();
        
        return redirect()->route('admin.guru.index')
                         ->with('success', 'Data guru berhasil dihapus!');
    }
}