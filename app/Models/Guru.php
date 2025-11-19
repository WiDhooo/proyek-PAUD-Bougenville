<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // <--- PASTIKAN IMPORT INI
use Illuminate\Support\Carbon; // <--- Import Carbon

class Guru extends Model
{
    protected $table = 'guru';
    protected $fillable = ['nama', 'username', 'password', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_hp', 'jabatan'];
    protected $appends = ['ttl'];
    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    /**
     * Menggabungkan tempat dan tanggal lahir
     */
    protected function ttl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = [];

                // 1. Tambahkan tempat lahir jika ada
                if (!empty($this->tempat_lahir)) {
                    $parts[] = $this->tempat_lahir;
                }

                // 2. Tambahkan tanggal lahir jika ada (sudah jadi objek Carbon karena $casts)
                if ($this->tanggal_lahir) {
                    // Format tanggalnya di sini (misal: 16 November 2025)
                    $parts[] = $this->tanggal_lahir->format('d F Y');
                }

                // 3. Gabungkan dengan koma, atau kembalikan '-' jika keduanya kosong
                return !empty($parts) ? implode(', ', $parts) : '-';
            },
        );
    }
}