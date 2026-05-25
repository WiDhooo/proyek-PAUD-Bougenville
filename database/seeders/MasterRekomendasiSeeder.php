<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterRekomendasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'label_cluster' => '0',
                'nama_kelompok' => 'Kelompok Butuh Stimulasi Motorik',
                'deskripsi_gaya_belajar' => 'Anak cenderung pasif dalam kegiatan fisik. Membutuhkan dorongan lebih untuk bergerak aktif.',
                'saran_kegiatan' => 'Ajak anak bermain lempar tangkap bola, meniti balok titian, atau kegiatan outbound sederhana.',
            ],
            [
                'label_cluster' => '1',
                'nama_kelompok' => 'Kelompok Seimbang (Agama & Sosial Tinggi)',
                'deskripsi_gaya_belajar' => 'Anak memiliki kecerdasan intrapersonal dan interpersonal yang baik. Sangat peka terhadap lingkungan.',
                'saran_kegiatan' => 'Libatkan dalam kegiatan bercerita (role play), kegiatan sosial/berbagi, dan memimpin doa.',
            ],
            [
                'label_cluster' => '2',
                'nama_kelompok' => 'Kelompok Unggul Kognitif & Bahasa',
                'deskripsi_gaya_belajar' => 'Anak sangat cepat menangkap konsep baru dan verbal. Cenderung analitis.',
                'saran_kegiatan' => 'Berikan permainan puzzle yang lebih kompleks, membacakan buku cerita, dan permainan tebak kata.',
            ],
            [
                'label_cluster' => '3',
                'nama_kelompok' => 'Kelompok Potensi Seni & Kreativitas',
                'deskripsi_gaya_belajar' => 'Anak mengekspresikan diri melalui visual dan gerakan. Imajinatif.',
                'saran_kegiatan' => 'Fasilitasi dengan alat menggambar lengkap, bermain musik, atau menari.',
            ],
            [
                'label_cluster' => '4',
                'nama_kelompok' => 'Kelompok Generalis (Serba Bisa)',
                'deskripsi_gaya_belajar' => 'Anak berkembang merata di semua aspek. Tidak ada kelemahan mencolok, potensi di semua bidang.',
                'saran_kegiatan' => 'Berikan tantangan yang lebih kompleks di semua bidang: proyek sains mini, pertunjukan seni, kepemimpinan kelas, dan eksplorasi lintas bidang.',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\MasterRekomendasi::create($item);
        }
    }
}
