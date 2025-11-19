<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\ProfilController;

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

    // CRUD Guru
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.destroy');

    // CRUD Murid
    Route::get('/murid', [SiswaController::class, 'index'])->name('murid.index');
    Route::post('/murid', [SiswaController::class, 'store'])->name('murid.store');
    Route::put('/murid/{id}', [SiswaController::class, 'update'])->name('murid.update');
    Route::delete('/murid/{id}', [SiswaController::class, 'destroy'])->name('murid.destroy');

    // CRUD Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('kelas.show');
    Route::post('/kelas/{id}/assign-murid', [KelasController::class, 'assignMurid'])->name('kelas.assign');
    Route::delete('/kelas/{id}/unassign-murid/{muridId}', [KelasController::class, 'unassignMurid'])->name('kelas.unassign');

    // Manajemen Konten
    // GET /admin/profil
    // Menampilkan daftar/ringkasan profil (digunakan di tombol "Kembali")
    // Nama rute: admin.profil.index
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');

    // GET /admin/profil/{id}/edit
    // Menampilkan form untuk mengedit profil tertentu.
    // Nama rute: admin.profil.edit
    Route::get('/profil/{id}/edit', [ProfilController::class, 'edit'])->name('profil.edit');

    // PUT/PATCH /admin/profil/{id}
    // Mengirim data form untuk memperbarui profil.
    // Nama rute: admin.profil.update (Action form di blade)
    Route::put('/profil/{id}', [ProfilController::class, 'update'])->name('profil.update');
    
    Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
    Route::post('/galeri', [GaleriController::class, 'store'])->name('galeri.store');
    Route::delete('/galeri/{id}', [GaleriController::class, 'destroy'])->name('galeri.destroy');
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
