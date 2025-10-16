<?php

use Illuminate\Support\Facades\Route;

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
