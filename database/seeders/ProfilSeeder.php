<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profil;
use App\Models\VisiMisi;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Buat satu data Profil utama (Asumsi ID=1)
        $profil = Profil::create([
            'tentang_sekolah' => 'PAUD Bougenville adalah lembaga pendidikan anak usia dini yang berkomitmen untuk menciptakan lingkungan belajar yang menyenangkan, aman, dan penuh kasih sayang. Berdiri dengan semangat mencerdaskan generasi penerus bangsa, kami percaya bahwa setiap anak memiliki potensi luar biasa yang perlu dikembangkan sejak dini melalui pendidikan yang tepat dan menyenangkan. 
            
            Dengan pendekatan "Belajar sambil Bermain", kami menciptakan pengalaman belajar yang bermakna dan menyenangkan bagi setiap anak. Fasilitas yang lengkap dan guru-guru yang berpengalaman menjadikan PAUD Bougenville pilihan tepat untuk mendampingi tumbuh kembang putra-putri Anda.',
        ]);

        // 2. Isi data Visi
        $visi_data = [
            'Mensosialisasikan, meningkatkan mutu, minat, bermain dan belajar anak usia dini dilingkungan sekitar khususnya dan luar pada umumnya.',
        ];

        foreach ($visi_data as $isi) {
            VisiMisi::create([
                'profil_id' => $profil->id,
                'tipe' => 'visi',
                'isi' => $isi,
            ]);
        }

        // 3. Isi data Misi
        $misi_data = [
            'Mengembangkan potensi anak didik',
            'Menjadikan anak yang sehat, cerdas, ceria',
            'Menjadikan anak untuk kreatif dan mandiri',
            'Menjadikan anak yang bertaqwa kepada Tuhan YME',
            'Menjadikan anak yang berkarakter'
        ];

        foreach ($misi_data as $isi) {
            VisiMisi::create([
                'profil_id' => $profil->id,
                'tipe' => 'misi',
                'isi' => $isi,
            ]);
        }
    }
}