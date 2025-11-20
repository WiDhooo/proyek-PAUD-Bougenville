<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Guru extends Model
{
    protected $table = 'guru';
    
    protected $fillable = [
        'user_id',
        'tempat_lahir', 
        'tanggal_lahir', 
        'alamat', 
        'no_hp', 
        'jabatan'
    ];
    
    protected $appends = ['ttl', 'nama']; // Tambahkan 'nama' sebagai accessor
    
    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    // Accessor untuk mengambil nama dari relasi user
    protected function nama(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user->name ?? '-',
        );
    }

    // Accessor untuk Tempat/Tanggal Lahir
    protected function ttl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = [];

                if (!empty($this->tempat_lahir)) {
                    $parts[] = $this->tempat_lahir;
                }

                if ($this->tanggal_lahir) {
                    $parts[] = $this->tanggal_lahir->format('d F Y');
                }

                return !empty($parts) ? implode(', ', $parts) : '-';
            },
        );
    }
}