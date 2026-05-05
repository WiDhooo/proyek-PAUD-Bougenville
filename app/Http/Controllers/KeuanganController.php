<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Illuminate\Http\Request;
use App\Models\Siswa;

class KeuanganController extends Controller
{
    public function index()
    {
        $data = Keuangan::with('siswa')->latest()->get();
        $siswas = Siswa::all(); 

        return view('admin.keuangan.index', compact('data', 'siswas'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Dasar & Batasan Nominal
        $request->validate([
            'tanggal' => 'required|date',
            'siswa_id' => 'required|exists:siswa,id',
            'bulan' => 'required|string',
            'tahun' => 'required|numeric',
            'jumlah' => 'required|numeric|min:20000|max:1000000', // Minimal 20rb, Maksimal 1jt
            'status' => 'required|in:Sudah Bayar,Belum Bayar',
        ], [
            'jumlah.required' => 'Mohon isi nominal pembayaran.',
            'jumlah.numeric'  => 'Nominal pembayaran harus berupa angka.',
            'jumlah.min'      => 'Nominal terlalu kecil. Batas minimal adalah Rp 20.000.',
            'jumlah.max'      => 'Nominal terlalu besar. Batas maksimal adalah Rp 1.000.000.',
            'siswa_id.required' => 'Silakan pilih nama siswa terlebih dahulu.',
            'bulan.required' => 'Pilih bulan periode pembayaran.',
            'status.required' => 'Mohon tentukan status pembayaran.',
        ]);

        $bulan_pembayaran = $request->bulan . ' ' . $request->tahun;

        // 2. VALIDASI DUPLIKASI: Cek apakah siswa yang sama sudah bayar di bulan & tahun yang sama
        $cekDuplikasi = Keuangan::where('siswa_id', $request->siswa_id)
                                ->where('bulan_pembayaran', $bulan_pembayaran)
                                ->exists();

        if ($cekDuplikasi) {
            return back()->withErrors([
                'bulan' => "Siswa ini sudah memiliki catatan pembayaran untuk periode $bulan_pembayaran. Harap periksa kembali."
            ])->withInput();
        }

        // 3. Validasi Periode Pendaftaran (Jangan kurang dari sama dengan pendaftaran)
        $pendaftaran = Keuangan::where('siswa_id', $request->siswa_id)
                                ->where('kategori', 'Pendaftaran')
                                ->first();

        if ($pendaftaran) {
            $parts = explode(' ', $pendaftaran->bulan_pembayaran);
            $bulanDaftar = $parts[0];
            $tahunDaftar = (int) $parts[1];

            $monthMap = [
                'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
                'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
                'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
            ];

            $inputMonthVal = $monthMap[$request->bulan];
            $daftarMonthVal = $monthMap[$bulanDaftar];

            if ($request->tahun < $tahunDaftar || ($request->tahun == $tahunDaftar && $inputMonthVal <= $daftarMonthVal)) {
                return back()->withErrors([
                    'bulan' => "Pembayaran hanya boleh dilakukan setelah bulan pendaftaran ($bulanDaftar $tahunDaftar)."
                ])->withInput();
            }
        }

        // 4. Simpan Data
        Keuangan::create([
            'tanggal' => $request->tanggal,
            'kategori' => 'SPP',
            'siswa_id' => $request->siswa_id,
            'bulan_pembayaran' => $bulan_pembayaran,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Data pembayaran SPP berhasil dicatat.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:20000|max:1000000',
            'status' => 'required|in:Sudah Bayar,Belum Bayar',
        ]);

        $keuangan = Keuangan::findOrFail($id);
        $keuangan->update([
            'jumlah' => $request->jumlah,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Keuangan::findOrFail($id)->delete();
        return back()->with('success', 'Data keuangan berhasil dihapus.');
    }
}