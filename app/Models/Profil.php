<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $table = 'profil';
    protected $fillable = ['tentang_sekolah'];

    public function visiMisi()
    {
        // Menyatakan relasi One-to-Many
        return $this->hasMany(VisiMisi::class, 'profil_id');
    }
}
