<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiAbsensi extends Model
{
    protected $table = 'nilai_absensi';
    protected $fillable = ['siswa_id', 'absensi', 'nilai', 'catatan'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
