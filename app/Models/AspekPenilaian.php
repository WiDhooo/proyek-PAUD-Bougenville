<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AspekPenilaian extends Model
{
    protected $table = 'aspek_penilaians';

    protected $fillable = [
        'lingkup',
        'sub_lingkup',
        'indikator',
    ];
}
