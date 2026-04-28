<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    use HasFactory;

    protected $table = 'ebooks';

    protected $fillable = [
        'judul',           // Judul buku
        'deskripsi',       // Deskripsi buku
        'file_path',       // Array JSON berisi path file gambar konten
        'thumbnail',       // Path file sampul buku (opsional)
        'tipe_file',       // Tipe file (default: multiple_images)
        'ukuran_file',     // Total ukuran file dalam byte
    ];

    protected $casts = [
        'file_path' => 'array',
    ];
}