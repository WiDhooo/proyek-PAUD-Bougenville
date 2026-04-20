<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilAnalisis extends Model
{
    protected $fillable = [
        'siswa_id',
        'cluster_group',
        'periode',
        'tahun_ajaran',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
