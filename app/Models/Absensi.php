<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'jadwal_id',
        'siswa_id',
        'tanggal',
        'status',
        'keterangan',
        'periode',
        'tahun_ajaran',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
