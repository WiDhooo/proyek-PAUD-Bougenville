<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\ProfilController;


/*
|--------------------------------------------------------------------------
| ROUTE UNTUK PENGUNJUNG (COMPANY PROFILE)
|--------------------------------------------------------------------------
*/
// Halaman Beranda
Route::get('/', function () {
    return view('beranda');
});

// Halaman Tentang Kami
Route::get('/tentang', function () {
    return view('tentang');
});

// Halaman Kegiatan
Route::get('/kegiatan', function () {
    return view('kegiatan');
});

// Halaman Kontak
Route::get('/kontak', function () {
    return view('kontak');
});

/*
|--------------------------------------------------------------------------
| ROUTE UNTUK AUTENTIKASI (LOGIN & LOGOUT)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    // Rute ini untuk MENAMPILKAN halaman login
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // Rute ini untuk MEMPROSES login (nanti diisi Back-End)
    // Kita simulasikan login sukses sebagai Admin
    return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali!');
})->name('login.attempt');

Route::post('/logout', function () {
    // Rute ini untuk MEMPROSES logout (nanti diisi Back-End)
    // Kita simulasikan logout sukses
    return redirect()->route('login')->with('success', 'Anda berhasil logout.');
})->name('logout');

/*
|--------------------------------------------------------------------------
| ROUTE UNTUK ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', function () {
        $dashboardData = [
            'total_murid' => 57, 'total_guru' => 12, 'total_kelas' => 3,
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
| ROUTE UNTUK GURU
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/data-siswa', [GuruController::class, 'dataSiswa'])->name('data_siswa');

    // Pilih Kelas sebelum input nilai & absensi
    Route::get('/nilai-absensi/pilih-kelas', [GuruController::class, 'pilihKelas'])->name('nilai_absensi.pilih_kelas');

    // Nilai & Absensi
    Route::get('/nilai-absensi', [GuruController::class, 'pilihKelas'])->name('nilai_absensi');
    Route::get('/nilai-absensi/{kelas}', [GuruController::class, 'nilaiAbsensi'])->name('nilai_absensi.kelas');
    Route::post('/nilai-absensi/{kelas}/simpan', [GuruController::class, 'simpanNilaiAbsensi'])->name('nilai_absensi.simpan');
});



