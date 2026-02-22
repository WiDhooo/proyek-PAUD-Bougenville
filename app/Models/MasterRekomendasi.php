<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterRekomendasi extends Model
{
    protected $fillable = [
        'label_cluster',
        'nama_kelompok',
        'deskripsi_gaya_belajar',
        'saran_kegiatan',
    ];
    //
}
