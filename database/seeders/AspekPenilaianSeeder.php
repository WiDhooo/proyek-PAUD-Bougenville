<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AspekPenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // 1. Nilai Agama & Moral
            ['lingkup' => 'Agama & Moral', 'sub_lingkup' => 'Perilaku Agama', 'indikator' => 'Mengetahui agama yang dianutnya'],
            ['lingkup' => 'Agama & Moral', 'sub_lingkup' => 'Perilaku Agama', 'indikator' => 'Meniru gerakan beribadah dengan urutan yang benar'],
            ['lingkup' => 'Agama & Moral', 'sub_lingkup' => 'Perilaku Moral', 'indikator' => 'Mengucapkan salam dan membalas salam'],

            // 2. Fisik-Motorik
            ['lingkup' => 'Fisik-Motorik', 'sub_lingkup' => 'Motorik Kasar', 'indikator' => 'Melakukan gerakan tubuh secara terkoordinasi (berlari, melompat)'],
            ['lingkup' => 'Fisik-Motorik', 'sub_lingkup' => 'Motorik Halus', 'indikator' => 'Menggambar sesuai gagasannya'],
            ['lingkup' => 'Fisik-Motorik', 'sub_lingkup' => 'Kesehatan', 'indikator' => 'Melakukan kebiasaan hidup bersih dan sehat'],

            // 3. Kognitif
            ['lingkup' => 'Kognitif', 'sub_lingkup' => 'Belajar & Pemecahan Masalah', 'indikator' => 'Mengenal benda berdasarkan fungsi'],
            ['lingkup' => 'Kognitif', 'sub_lingkup' => 'Berpikir Logis', 'indikator' => 'Mengenal konsep bilangan'],
            ['lingkup' => 'Kognitif', 'sub_lingkup' => 'Berpikir Simbolik', 'indikator' => 'Mengenal lambang huruf'],

            // 4. Bahasa
            ['lingkup' => 'Bahasa', 'sub_lingkup' => 'Memahami Bahasa', 'indikator' => 'Menyimak perkataan orang lain'],
            ['lingkup' => 'Bahasa', 'sub_lingkup' => 'Mengungkapkan Bahasa', 'indikator' => 'Mengulang kalimat sederhana'],
            ['lingkup' => 'Bahasa', 'sub_lingkup' => 'Keaksaraan', 'indikator' => 'Menyebutkan simbol-simbol huruf yang dikenal'],

            // 5. Sosial-Emosional
            ['lingkup' => 'Sosial-Emosional', 'sub_lingkup' => 'Kesadaran Diri', 'indikator' => 'Memperlihatkan kemampuan diri untuk menyesuaikan dengan situasi'],
            ['lingkup' => 'Sosial-Emosional', 'sub_lingkup' => 'Tanggung Jawab', 'indikator' => 'Mentaati aturan kelas'],
            ['lingkup' => 'Sosial-Emosional', 'sub_lingkup' => 'Perilaku Prososial', 'indikator' => 'Bermain dengan teman sebaya'],

            // 6. Seni
            ['lingkup' => 'Seni', 'sub_lingkup' => 'Eksplorasi Seni', 'indikator' => 'Senandung lagu anak-anak'],
            ['lingkup' => 'Seni', 'sub_lingkup' => 'Ekspresi Seni', 'indikator' => 'Tertarik dengan kegiatan musik, gerakan orang, hewan maupun tumbuhan'],
        ];

        foreach ($data as $item) {
            \App\Models\AspekPenilaian::create($item);
        }
    }
}
