<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Tambahkan ini

class Siswa extends Model
{
    use HasFactory; // <-- Tambahkan ini

    protected $table = 'siswa';
    protected $fillable = ['nis', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'kelas_id'];
    protected $appends = ['usia'];
    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
    ];

    protected function usia(): Attribute {
        return Attribute::make(
            // Hapus " . ' tahun'" agar sorting di tabel berfungsi
            get: fn () => $this->tanggal_lahir ? Carbon::parse($this->tanggal_lahir)->age : '-',
        );
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}