<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route untuk halaman utama, langsung diarahkan ke dashboard admin
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Mengelompokkan semua route yang hanya bisa diakses oleh Admin
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', function () {
        $dashboardData = [
            'total_murid' => 57,
            'total_guru' => 12,
            'total_kelas' => 3,
            'jadwal' => [
                'Senin' => ['Mandiri - A' => 'Galar Widodo', 'Ceria - B' => 'Xanana Megantara', 'Kreatif - A' => 'Gaman Maras Saputra'],
                'Selasa' => ['Mandiri - A' => 'Qori Usada M.Pd', 'Ceria - B' => 'Zalindra Rahayu', 'Kreatif - A' => 'Bakti Jarwadi S. M.T.'],
                'Rabu' => ['Mandiri - A' => 'Caraka Sabar Waskita S.E.I', 'Ceria - B' => 'Sari Kuswandari', 'Kreatif - A' => 'Pardi Prasetya S.Kom'],
                'Kamis' => ['Mandiri - A' => 'Yulia Andriani S.Ked', 'Ceria - B' => 'Dipa Cakra Buana', 'Kreatif - A' => 'Taufan Habibi M.T.I.'],
                'Jumat' => ['Mandiri - A' => 'Galar Widodo', 'Ceria - B' => 'Sari Kuswandari', 'Kreatif - A' => 'Cahyadi Bahuraksa D.'],
            ]
        ];
        return view('admin.dashboard', ['data' => $dashboardData]);
    })->name('dashboard');

    // CRUD Guru
    Route::get('/guru', function () {
        $data_guru_palsu = [
            ['id' => 1, 'nama' => 'Devi Hariyah', 'jabatan' => 'Guru', 'alamat' => 'Jl. Pendidikan No. 1', 'pendidikan' => 'S1'],
            ['id' => 2, 'nama' => 'Jayeng Wawan Pradipta S.E.I', 'jabatan' => 'Guru', 'alamat' => 'Jr. Cut Nyak Dien No. 956', 'pendidikan' => 'D3'],
            ['id' => 3, 'nama' => 'Victoria Pertiwi', 'jabatan' => 'Staff', 'alamat' => 'KI. Panjaitan No. 78', 'pendidikan' => 'D3'],
        ];
        return view('admin.guru.index', ['guru' => $data_guru_palsu]);
    })->name('guru.index');
    Route::post('/guru', function () { return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil ditambahkan!'); })->name('guru.store');
    Route::put('/guru/{id}', function ($id) { return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui!'); })->name('guru.update');
    Route::delete('/guru/{id}', function ($id) { return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil dihapus!'); })->name('guru.destroy');

    // CRUD Murid
    Route::get('/murid', function () {
        $data_murid_palsu = [
            ['id' => 1, 'nik' => '25190527237', 'nama' => 'Rogelio Torphy', 'usia' => '6 tahun', 'jenis_kelamin' => 'Laki-laki'],
            ['id' => 2, 'nik' => '25130909649', 'nama' => 'Marguerite McKenzie', 'usia' => '5 tahun', 'jenis_kelamin' => 'Perempuan'],
        ];
        return view('admin.murid.index', ['murid' => $data_murid_palsu]);
    })->name('murid.index');
    Route::post('/murid', function () { return redirect()->route('admin.murid.index')->with('success', 'Data murid berhasil ditambahkan!'); })->name('murid.store');
    Route::put('/murid/{id}', function ($id) { return redirect()->route('admin.murid.index')->with('success', 'Data murid berhasil diperbarui!'); })->name('murid.update');
    Route::delete('/murid/{id}', function ($id) { return redirect()->route('admin.murid.index')->with('success', 'Data murid berhasil dihapus!'); })->name('murid.destroy');

    // CRUD Kelas
    Route::get('/kelas', function () {
        $data_kelas_palsu = [
            ['id' => 1, 'nama_kelas' => 'Mandiri', 'kelas' => 'A', 'wali' => 'Jayeng Wawan Pradipta S.E.I'],
            ['id' => 2, 'nama_kelas' => 'Ceria', 'kelas' => 'B', 'wali' => 'Victoria Pertiwi'],
        ];
        return view('admin.kelas.index', ['kelas' => $data_kelas_palsu]);
    })->name('kelas.index');
    Route::post('/kelas', function () { return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan!'); })->name('kelas.store');
    Route::put('/kelas/{id}', function ($id) { return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui!'); })->name('kelas.update');
    Route::delete('/kelas/{id}', function ($id) { return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil dihapus!'); })->name('kelas.destroy');
    Route::get('/kelas/{id}', function ($id) {
        $kelas_detail = ['id' => $id, 'nama_kelas' => 'Mandiri', 'kelas' => 'A'];
        $murid_di_kelas = [['id' => 1, 'nis' => '2526530970', 'nama' => 'Zelda Maheswara', 'jenis_kelamin' => 'Perempuan']];
        $semua_murid = [['id' => 3, 'nik' => '25190910658', 'nama' => 'Prof. Reynold Trantow III']];
        return view('admin.kelas.show', ['kelas' => $kelas_detail, 'murid_di_kelas' => $murid_di_kelas, 'semua_murid' => $semua_murid]);
    })->name('kelas.show');
    Route::post('/kelas/{id}/assign-murid', function ($id) { return redirect()->route('admin.kelas.show', $id)->with('success', 'Murid berhasil ditambahkan!'); })->name('kelas.assign');
    Route::delete('/kelas/{id}/unassign-murid/{muridId}', function ($id, $muridId) { return redirect()->route('admin.kelas.show', $id)->with('success', 'Murid berhasil dihapus!'); })->name('kelas.unassign');

    // Manajemen Konten
    Route::get('/profil-sekolah', function () { return view('admin.profil.index'); })->name('profil.index');
    Route::get('/profil-sekolah/edit', function () {
        $profil_sekolah = [
            'visi' => 'Menjadi lembaga pendidikan usia dini yang unggul.',
            'misi' => '1. Menyelenggarakan pembelajaran yang aktif. 2. Mengembangkan potensi anak.',
            'sejarah' => 'Didirikan pada tahun 2010.',
        ];
        return view('admin.profil.edit', ['profil' => $profil_sekolah]);
    })->name('profil.edit');
    Route::post('/profil-sekolah/update', function () { return redirect()->route('admin.profil.edit')->with('success', 'Profil sekolah berhasil diperbarui!'); })->name('profil.update');
    
    Route::get('/galeri', function () {
        $foto = [['id' => 1, 'url' => 'https://via.placeholder.com/300x200.png/0077ff/FFFFFF?text=Kegiatan+1', 'judul' => 'Lomba 17 Agustus']];
        return view('admin.profil.galeri', ['foto' => $foto]);
    })->name('galeri.index');
    Route::post('/galeri', function () { return redirect()->route('admin.galeri.index')->with('success', 'Foto baru berhasil ditambahkan!'); })->name('galeri.store');
});

// ================= ROUTE UNTUK GURU =================
Route::prefix('guru')->name('guru.')->group(function () {
    
    // Halaman Dashboard Guru
    Route::get('/dashboard', function() {
        // Data jadwal palsu untuk satu guru
        $jadwal_guru = [
            'Senin' => ['08:00 - 09:30', 'Kelas Mandiri - A'],
            'Selasa' => ['10:00 - 11:30', 'Kelas Kreatif - A'],
            'Rabu' => ['08:00 - 09:30', 'Kelas Mandiri - A'],
            'Kamis' => ['10:00 - 11:30', 'Kelas Kreatif - A'],
            'Jumat' => ['08:00 - 09:30', 'Kelas Ceria - B'],
        ];
        return view('guru.dashboard', ['jadwal' => $jadwal_guru]);
    })->name('dashboard');

    // Halaman Data Siswa
    Route::get('/data-siswa', function() {
        // Data palsu murid yang diajar oleh guru ini
        $murid_kelas_ini = [
            ['id' => 1, 'nis' => '2526530201', 'nama' => 'Danu Purnawati', 'jenis_kelamin' => 'Perempuan'],
            ['id' => 2, 'nis' => '2526530142', 'nama' => 'Farah Utama', 'jenis_kelamin' => 'Laki-Laki'],
            ['id' => 3, 'nis' => '2526530037', 'nama' => 'Tiara Yulianti', 'jenis_kelamin' => 'Laki-Laki'],
        ];
        return view('guru.data_siswa', ['murid' => $murid_kelas_ini]);
    })->name('data_siswa'); // Pastikan namanya persis 'data_siswa'

    Route::get('/nilai-absensi', function() {
        // Data palsu murid yang sama seperti di halaman data siswa
        $murid_kelas_ini = [
            ['id' => 1, 'nis' => '2526530201', 'nama' => 'Danu Purnawati'],
            ['id' => 2, 'nis' => '2526530142', 'nama' => 'Farah Utama'],
            ['id' => 3, 'nis' => '2526530037', 'nama' => 'Tiara Yulianti'],
        ];
        return view('guru.nilai_absensi', ['murid' => $murid_kelas_ini]);
    })->name('nilai_absensi');

});

