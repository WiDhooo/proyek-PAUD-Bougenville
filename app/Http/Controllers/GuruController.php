<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\NilaiAbsensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    // ==================== DASHBOARD GURU ====================
    
    public function dashboard()
    {
        $guruId = Auth::id();
        
        $jadwal = Jadwal::with('kelas')
                       ->where('guru_id', Auth::user()->guru->id ?? 0)
                       ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
                       ->get();

        return view('guru.dashboard', compact('jadwal'));
    }

    public function dataSiswa()
    {
        $guruId = Auth::user()->guru->id ?? 0;
        
        $kelasIds = Jadwal::where('guru_id', $guruId)
                          ->pluck('kelas_id')
                          ->unique();
        
        $siswa = Siswa::whereIn('kelas_id', $kelasIds)->get();
        
        return view('guru.data_siswa', compact('siswa'));
    }

    public function pilihKelas()
    {
        $guruId = Auth::user()->guru->id ?? 0;
        
        $kelasIds = Jadwal::where('guru_id', $guruId)
                          ->pluck('kelas_id')
                          ->unique();
        
        $kelas = Kelas::whereIn('id', $kelasIds)->get();
        
        return view('guru.pilih_kelas', compact('kelas'));
    }

    public function nilaiAbsensi($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $murid = $kelas->siswa;
        
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

    // ==================== ADMIN CRUD GURU ====================
    
    public function index()
    {
        $gurus = Guru::with('user')->get()->map(function($guru) {
            return [
                'id' => $guru->id,
                'user_id' => $guru->user_id,
                'nama' => $guru->nama, // Dari accessor
                'email' => $guru->user->email ?? '-',
                'tempat_lahir' => $guru->tempat_lahir,
                'tanggal_lahir' => $guru->tanggal_lahir,
                'ttl' => $guru->ttl,
                'no_hp' => $guru->no_hp,
                'alamat' => $guru->alamat,
                'jabatan' => $guru->jabatan,
            ];
        });
        
        return view('admin.guru.index', compact('gurus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'jabatan' => ['required', 'string', Rule::in(['Kepala Sekolah', 'Sekretaris', 'Bendahara', 'Pendidik'])]
        ]);

        // 1. Buat User terlebih dahulu
        $user = User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']), // Tambahkan bcrypt()
            'role' => 'guru',
        ]);

        // 2. Buat Guru dengan user_id
        Guru::create([
            'user_id' => $user->id,
            'tempat_lahir' => $data['tempat_lahir'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'no_hp' => $data['no_hp'],
            'alamat' => $data['alamat'],
            'jabatan' => $data['jabatan'],
        ]);

        return redirect()->route('admin.guru.index')
                         ->with('success', 'Data Guru berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::with('user')->findOrFail($id);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($guru->user_id)
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

        // 1. Update User
        $guru->user->update([
            'name' => $data['nama'],
            'email' => $data['email'],
        ]);

        // Update password jika diisi
        if (!empty($data['password'])) {
            $guru->user->update([
                'password' => bcrypt($data['password']) // Tambahkan bcrypt()
            ]);
        }

        // 2. Update Guru
        $guru->update([
            'tempat_lahir' => $data['tempat_lahir'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'no_hp' => $data['no_hp'],
            'alamat' => $data['alamat'],
            'jabatan' => $data['jabatan'],
        ]);

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        
        // Hapus user (akan cascade delete guru)
        $guru->user->delete();
        
        return redirect()->route('admin.guru.index')
                         ->with('success', 'Data guru berhasil dihapus!');
    }
}