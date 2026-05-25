<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AspekPenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Indikator berdasarkan STPPA Permendikbud No. 137 Tahun 2014
     * Target kelompok: Usia 5-6 Tahun (Kelompok B PAUD)
     */
    public function run(): void
    {
        $data = [
            // ============================================================
            // 1. Nilai Agama & Moral (6 indikator)
            // ============================================================
            [
                'lingkup'     => 'Agama & Moral',
                'sub_lingkup' => 'Ibadah & Keagamaan',
                'indikator'   => 'Mengenal agama yang dianut dan dapat menyebutkan nama Tuhan',
            ],
            [
                'lingkup'     => 'Agama & Moral',
                'sub_lingkup' => 'Ibadah & Keagamaan',
                'indikator'   => 'Mengerjakan ibadah sesuai agamanya (berdoa, gerakan sholat, dll)',
            ],
            [
                'lingkup'     => 'Agama & Moral',
                'sub_lingkup' => 'Perilaku Moral',
                'indikator'   => 'Berperilaku jujur, sopan, hormat, dan sportif dalam keseharian',
            ],
            [
                'lingkup'     => 'Agama & Moral',
                'sub_lingkup' => 'Perilaku Moral',
                'indikator'   => 'Menjaga kebersihan diri dan lingkungan sekitar',
            ],
            [
                'lingkup'     => 'Agama & Moral',
                'sub_lingkup' => 'Toleransi',
                'indikator'   => 'Menghormati teman yang berbeda agama atau kepercayaan',
            ],
            [
                'lingkup'     => 'Agama & Moral',
                'sub_lingkup' => 'Perilaku Moral',
                'indikator'   => 'Mengucapkan salam dan membalas salam dengan benar',
            ],

            // ============================================================
            // 2. Fisik-Motorik (8 indikator)
            // ============================================================
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Motorik Kasar',
                'indikator'   => 'Melakukan gerakan tubuh secara terkoordinasi (berlari, melompat, berguling)',
            ],
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Motorik Kasar',
                'indikator'   => 'Melakukan koordinasi gerakan mata-kaki-tangan dalam menirukan tarian atau senam',
            ],
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Motorik Kasar',
                'indikator'   => 'Melakukan permainan fisik dengan aturan (misal: engklek, lompat tali)',
            ],
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Motorik Halus',
                'indikator'   => 'Menggambar sesuai gagasannya dengan detail',
            ],
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Motorik Halus',
                'indikator'   => 'Menggunakan alat tulis dan alat makan dengan benar',
            ],
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Motorik Halus',
                'indikator'   => 'Menggunting sesuai pola dan menempel gambar dengan tepat',
            ],
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Kesehatan & Keselamatan',
                'indikator'   => 'Melakukan kebiasaan hidup bersih dan sehat (mencuci tangan, gosok gigi)',
            ],
            [
                'lingkup'     => 'Fisik-Motorik',
                'sub_lingkup' => 'Kesehatan & Keselamatan',
                'indikator'   => 'Mengetahui situasi yang membahayakan diri dan cara menghindarinya',
            ],

            // ============================================================
            // 3. Kognitif (8 indikator)
            // ============================================================
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Belajar & Pemecahan Masalah',
                'indikator'   => 'Mengenal benda berdasarkan fungsi (pisau untuk memotong, pensil untuk menulis)',
            ],
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Belajar & Pemecahan Masalah',
                'indikator'   => 'Memecahkan masalah sederhana dalam kehidupan sehari-hari secara fleksibel',
            ],
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Belajar & Pemecahan Masalah',
                'indikator'   => 'Menunjukkan sikap kreatif dan ingin tahu dalam menyelidiki sesuatu',
            ],
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Berpikir Logis',
                'indikator'   => 'Mengklasifikasikan benda berdasarkan warna, bentuk, dan ukuran (3 variasi)',
            ],
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Berpikir Logis',
                'indikator'   => 'Mengenal sebab-akibat tentang lingkungannya (misal: angin menyebabkan daun bergerak)',
            ],
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Berpikir Logis',
                'indikator'   => 'Mengurutkan benda berdasarkan ukuran dari paling kecil ke paling besar',
            ],
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Berpikir Simbolik',
                'indikator'   => 'Menyebutkan lambang bilangan 1–10 dan menggunakannya untuk menghitung',
            ],
            [
                'lingkup'     => 'Kognitif',
                'sub_lingkup' => 'Berpikir Simbolik',
                'indikator'   => 'Mengenal berbagai macam lambang huruf vokal dan konsonan',
            ],

            // ============================================================
            // 4. Bahasa (7 indikator)
            // ============================================================
            [
                'lingkup'     => 'Bahasa',
                'sub_lingkup' => 'Memahami Bahasa',
                'indikator'   => 'Menyimak dan memahami perkataan orang lain serta cerita yang dibacakan',
            ],
            [
                'lingkup'     => 'Bahasa',
                'sub_lingkup' => 'Memahami Bahasa',
                'indikator'   => 'Memahami aturan dalam suatu permainan dan mengikutinya',
            ],
            [
                'lingkup'     => 'Bahasa',
                'sub_lingkup' => 'Mengungkapkan Bahasa',
                'indikator'   => 'Mengulang kalimat yang lebih kompleks dan menjawab pertanyaan dengan tepat',
            ],
            [
                'lingkup'     => 'Bahasa',
                'sub_lingkup' => 'Mengungkapkan Bahasa',
                'indikator'   => 'Berkomunikasi secara lisan dan menceritakan kembali isi cerita sederhana',
            ],
            [
                'lingkup'     => 'Bahasa',
                'sub_lingkup' => 'Keaksaraan',
                'indikator'   => 'Menyebutkan simbol-simbol huruf yang dikenal',
            ],
            [
                'lingkup'     => 'Bahasa',
                'sub_lingkup' => 'Keaksaraan',
                'indikator'   => 'Memahami hubungan antara bunyi dan bentuk huruf',
            ],
            [
                'lingkup'     => 'Bahasa',
                'sub_lingkup' => 'Keaksaraan',
                'indikator'   => 'Membaca dan menuliskan namanya sendiri',
            ],

            // ============================================================
            // 5. Sosial-Emosional (7 indikator)
            // ============================================================
            [
                'lingkup'     => 'Sosial-Emosional',
                'sub_lingkup' => 'Kesadaran Diri',
                'indikator'   => 'Memperlihatkan kemampuan diri untuk menyesuaikan dengan situasi baru',
            ],
            [
                'lingkup'     => 'Sosial-Emosional',
                'sub_lingkup' => 'Kesadaran Diri',
                'indikator'   => 'Mengenal perasaan sendiri dan mengelolanya secara wajar (mengendalikan diri)',
            ],
            [
                'lingkup'     => 'Sosial-Emosional',
                'sub_lingkup' => 'Tanggung Jawab',
                'indikator'   => 'Mentaati aturan kelas dan mengatur diri sendiri',
            ],
            [
                'lingkup'     => 'Sosial-Emosional',
                'sub_lingkup' => 'Tanggung Jawab',
                'indikator'   => 'Bertanggung jawab atas perilakunya untuk kebaikan diri sendiri dan orang lain',
            ],
            [
                'lingkup'     => 'Sosial-Emosional',
                'sub_lingkup' => 'Perilaku Prososial',
                'indikator'   => 'Bermain dengan teman sebaya dan mengetahui perasaan temannya',
            ],
            [
                'lingkup'     => 'Sosial-Emosional',
                'sub_lingkup' => 'Perilaku Prososial',
                'indikator'   => 'Berbagi dengan orang lain dan menghargai pendapat atau karya teman',
            ],
            [
                'lingkup'     => 'Sosial-Emosional',
                'sub_lingkup' => 'Perilaku Prososial',
                'indikator'   => 'Bersikap kooperatif dan menunjukkan sikap toleran terhadap perbedaan',
            ],

            // ============================================================
            // 6. Seni (6 indikator)
            // ============================================================
            [
                'lingkup'     => 'Seni',
                'sub_lingkup' => 'Ekspresi Musik',
                'indikator'   => 'Bersenandung atau bernyanyi lagu anak-anak dengan irama yang benar',
            ],
            [
                'lingkup'     => 'Seni',
                'sub_lingkup' => 'Ekspresi Musik',
                'indikator'   => 'Memainkan alat musik atau benda yang menghasilkan irama bersama teman',
            ],
            [
                'lingkup'     => 'Seni',
                'sub_lingkup' => 'Ekspresi Visual',
                'indikator'   => 'Menggambar berbagai macam bentuk yang beragam dan melukis dengan berbagai cara',
            ],
            [
                'lingkup'     => 'Seni',
                'sub_lingkup' => 'Ekspresi Visual',
                'indikator'   => 'Membuat karya dari berbagai bahan (kertas, plastisin, balok, dll)',
            ],
            [
                'lingkup'     => 'Seni',
                'sub_lingkup' => 'Ekspresi Gerak & Drama',
                'indikator'   => 'Mengekspresikan gerakan dengan irama yang bervariasi (tari, senam)',
            ],
            [
                'lingkup'     => 'Seni',
                'sub_lingkup' => 'Ekspresi Gerak & Drama',
                'indikator'   => 'Tertarik dan berpartisipasi dalam bermain drama atau permainan peran sederhana',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\AspekPenilaian::create($item);
        }
    }
}
