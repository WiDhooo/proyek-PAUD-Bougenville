<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\GuruAdminController;
use App\Http\Controllers\Admin\MuridAdminController;

/*
|--------------------------------------------------------------------------
| ROUTE PENGUNJUNG (COMPANY PROFILE)
|--------------------------------------------------------------------------
*/

Route::view('/', 'beranda');
Route::view('/tentang', 'tentang');
Route::view('/kegiatan', 'kegiatan');
Route::view('/kontak', 'kontak');

/*
|--------------------------------------------------------------------------
| LOGIN & AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class, 'attempt'])
    ->name('login.attempt')
    ->middleware('guest');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| ROUTE ADMIN (Proteksi role admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

    /* DASHBOARD */
    Route::get('/dashboard', function () {
        $dashboardData = [
            'total_murid' => 57,
            'total_guru' => 12,
            'total_kelas' => 3,
            'jadwal' => [
                'Senin' => [
                    'Mandiri - A' => 'Galar Widodo',
                    'Ceria - B' => 'Xanana Megantara',
                    'Kreatif - A' => 'Gaman Maras Saputra',
                ],
                'Selasa' => [
                    'Mandiri - A' => 'Qori Usada M.Pd',
                    'Ceria - B' => 'Zalindra Rahayu',
                    'Kreatif - A' => 'Bakti Jarwadi S. M.T.',
                ],
            ],
        ];

        return view('admin.dashboard', ['data' => $dashboardData]);
    })->name('dashboard');

    /* DATA GURU */
    Route::view('/guru', 'admin.guru.index')->name('guru.index');
    Route::post('/guru', fn () => back()->with('success', 'Data guru ditambahkan!'))->name('guru.store');
    Route::put('/guru/{id}', fn () => back()->with('success', 'Data guru diperbarui!'))->name('guru.update');
    Route::delete('/guru/{id}', fn () => back()->with('success', 'Data guru dihapus!'))->name('guru.destroy');

    /* DATA SISWA */
    Route::view('/murid', 'admin.murid.index')->name('murid.index');
    Route::post('/murid', fn () => back()->with('success', 'Data murid ditambahkan!'))->name('murid.store');
    Route::put('/murid/{id}', fn () => back()->with('success', 'Data murid diperbarui!'))->name('murid.update');
    Route::delete('/murid/{id}', fn () => back()->with('success', 'Data murid dihapus!'))->name('murid.destroy');

    // CRUD Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    //     $data_kelas_palsu = [
    //         ['id' => 1, 'nama_kelas' => 'Mandiri', 'kelas' => 'A', 'wali' => 'Jayeng Wawan Pradipta S.E.I'],
    //         ['id' => 2, 'nama_kelas' => 'Ceria', 'kelas' => 'B', 'wali' => 'Victoria Pertiwi'],
    //     ];
    //     return view('admin.kelas.index', ['kelas' => $data_kelas_palsu]);
    // })->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', function ($id) { return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil dihapus!'); })->name('kelas.destroy');
    Route::get('/kelas/{id}', function ($id) {
        $kelas_detail = ['id' => $id, 'nama_kelas' => 'Mandiri', 'kelas' => 'A'];
        $murid_di_kelas = [['id' => 1, 'nis' => '2526530970', 'nama' => 'Zelda Maheswara', 'jenis_kelamin' => 'Perempuan']];
        $semua_murid = [['id' => 3, 'nik' => '25190910658', 'nama' => 'Prof. Reynold Trantow III']];
        return view('admin.kelas.show', ['kelas' => $kelas_detail, 'murid_di_kelas' => $murid_di_kelas, 'semua_murid' => $semua_murid]);
    })->name('kelas.show');
    Route::post('/kelas/{id}/assign-murid', function ($id) { return redirect()->route('admin.kelas.show', $id)->with('success', 'Murid berhasil ditambahkan!'); })->name('kelas.assign');
    Route::delete('/kelas/{id}/unassign-murid/{muridId}', function ($id, $muridId) { return redirect()->route('admin.kelas.show', $id)->with('success', 'Murid berhasil dihapus!'); })->name('kelas.unassign');

    /* Assign / Unassign murid */
    Route::post('/kelas/{id}/assign-murid', [KelasController::class, 'assign'])
        ->name('kelas.assign');

    Route::delete('/kelas/{kelas_id}/unassign-murid/{murid_id}', [KelasController::class, 'unassignMurid'])
        ->name('kelas.unassign');

    /* PROFIL SEKOLAH */
    Route::view('/profil-sekolah', 'admin.profil.index')->name('profil.index');
    Route::view('/profil-sekolah/edit', 'admin.profil.edit')->name('profil.edit');
    Route::post('/profil-sekolah/update', fn () => back()->with('success', 'Update berhasil!'))->name('profil.update');

    /* GALERI */
    Route::view('/galeri', 'admin.profil.galeri')->name('galeri.index');
    Route::post('/galeri', fn () => back()->with('success', 'Foto ditambahkan!'))->name('galeri.store');
});


/*
|--------------------------------------------------------------------------
| ROUTE GURU (Proteksi role guru)
|--------------------------------------------------------------------------
*/

Route::prefix('guru')
    ->name('guru.')
    ->middleware(['auth', 'role:guru'])
    ->group(function () {

    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/data-siswa', [GuruController::class, 'dataSiswa'])->name('data_siswa');

    Route::get('/nilai-absensi/pilih-kelas', [GuruController::class, 'pilihKelas'])->name('nilai_absensi.pilih_kelas');

    Route::get('/nilai-absensi', [GuruController::class, 'pilihKelas'])->name('nilai_absensi');
    Route::get('/nilai-absensi/{kelas}', [GuruController::class, 'nilaiAbsensi'])->name('nilai_absensi.kelas');
    Route::post('/nilai-absensi/{kelas}/simpan', [GuruController::class, 'simpanNilaiAbsensi'])->name('nilai_absensi.simpan');
});
