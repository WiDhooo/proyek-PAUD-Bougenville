<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    use HasFactory;

    protected $table = 'keuangans';
    protected $fillable = [
    'tanggal',
    'jenis',
    'siswa_id', // Menggunakan ID
    'jumlah',
    'bulan_pembayaran',
    ];

    // Relasi ke model Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
