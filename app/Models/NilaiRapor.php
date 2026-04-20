<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiRapor extends Model
{
    protected $fillable = [
        'siswa_id',
        'aspek_penilaian_id',
        'nilai',
        'periode',
        'tahun_ajaran',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function aspekPenilaian()
    {
        return $this->belongsTo(AspekPenilaian::class);
    }
}
