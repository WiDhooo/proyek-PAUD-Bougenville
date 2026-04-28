<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ebook;
use Illuminate\Support\Facades\DB;

class EbookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel sebelum mengisi untuk menghindari duplikasi ID
        DB::table('ebooks')->truncate();

        $data = [
            [
                'id' => 1,
                'judul' => 'Panduan Gerakan Bacaan Sholat',
                'deskripsi' => 'Belajar shalat dengan mudah melalui panduan interaktif dilengkapi bacaan, gerakan, dan ilustrasi yang menarik untuk anak-anak.',
                'file_path' => json_encode([
                    ['image' => 'ebooks/content/kdfDfTA585h2bFsEegsWS6EjolmxmJpULTPxA7JQ.png', 'audio' => null],
                    ['image' => 'ebooks/content/yAndQsbeKrILn215K3Epv3EmDiwp7ay0SR6GEurE.png', 'audio' => null],
                    ['image' => 'ebooks/content/hH2P8CrVv7pCAP59Dmx1TZC1M9A8jp4qAd4oAOHA.png', 'audio' => 'ebooks/audio/zzV4pofMdj2VLYj48FQC5hLPcNFp3FJUIliKDdTu.webm'],
                    ['image' => 'ebooks/content/tpndbwNRWALKyE60yuK992dj40lOuqt3CHeTQdKQ.png', 'audio' => 'ebooks/audio/4CewxAYaKV7lQs2Tbkoh2aSOhYKHw380lxgYh350.webm'],
                    ['image' => 'ebooks/content/YkBiMsrFTf1yxoa7VhTyxqyC98n09x9AG0qJx8Xi.png', 'audio' => 'ebooks/audio/xzUlHgI0fblx1sOp1E3bU1F69b0Lu9BXMdr8wkov.mp4'],
                    ['image' => 'ebooks/content/coXM8c45gh0plnWPPnyCn9L15U0QhjHA2yd2zaDJ.png', 'audio' => 'ebooks/audio/KKmbhYp5ryJrwjmcxdZhJH2KGYEdmKzEoZoUcvz2.webm'],
                    ['image' => 'ebooks/content/V2u1Gi9TIQSGToDoiC4hX6KXBx3aXFkVyncD0BPy.png', 'audio' => 'ebooks/audio/7hokZA9iXShZQ8rU31reFY7aTJ7OX56raNctUp2b.mp4'],
                    ['image' => 'ebooks/content/MaxeCrkplYhoxJvD5gPd7TgLc6xl9QEBB4KSdP10.png', 'audio' => 'ebooks/audio/cYrIoHV4NCEetrBp32y18NVFNU26QdNew52qtRx1.webm'],
                    ['image' => 'ebooks/content/kOYVq2dqDI7tyIsyxvbBJIoNs4K1kt7kGU4SFde3.png', 'audio' => 'ebooks/audio/SCn2lPgXXBkxbGpfQztJgEPRjGndSWyRPC3du8rr.webm'],
                    ['image' => 'ebooks/content/L7B454zNk88nwGsFZMqo5GNl68zR51wkaPeG3o49.png', 'audio' => 'ebooks/audio/gAQpf7V1GbckagXNlmsky8cPlmfQxt0dE4KTv6Yt.mp4'],
                    ['image' => 'ebooks/content/YXZN5n7CqIj4NxU1o0njSHZZ7zyFlM53FbwBF3q9.png', 'audio' => null],
                    ['image' => 'ebooks/content/FbfP4jNPe2JDZo6vYK8aPABjQhljcDJC5FSZMtga.png', 'audio' => null],
                ]),
                'thumbnail' => 'ebooks/thumbnails/gyEBxd38jCB7e97VtzZ9cJfsXju99clfX56D4kw2.png',
                'ukuran_file' => 25612574,
                'created_at' => '2026-04-24 17:06:39',
                'updated_at' => '2026-04-26 20:29:53',
            ],
            [
                'id' => 2,
                'judul' => 'Mengenal Hewan Lebih Dekat',
                'deskripsi' => 'Jelajahi dunia hewan melalui ebook interaktif yang penuh warna dan suara menarik. Cocok untuk mengenalkan anak pada berbagai jenis hewan.',
                'file_path' => json_encode([
                    ['image' => 'ebooks/content/rNaVZyRpY1AA6s4DHEy9t6yk6M0TgJkQrEHAqcPt.png', 'audio' => null],
                    ['image' => 'ebooks/content/wAfo5vzgZOlPtS0lqm0HBOujyBSqZ12WAuBeTE27.png', 'audio' => null],
                    ['image' => 'ebooks/content/OxMHxYtOL6j9jwlpVDygMZThROAJX1otKQItqlMg.png', 'audio' => null],
                    ['image' => 'ebooks/content/Vpr0Z7Mc55w9FPggMS1CrV4194UtO5mUDpO356P7.png', 'audio' => null],
                    ['image' => 'ebooks/content/sYawfAD3a5impJJOfHbcvnSuME6wafW2jdW12Gqs.png', 'audio' => null],
                    ['image' => 'ebooks/content/f7xtOyhgN5fAzu6uhnCmuJapSNoDd8G29UpTA4eL.png', 'audio' => null],
                    ['image' => 'ebooks/content/ax5706MVKbsTIxM9PxJEMDZgf8fuPJ9mKRdsnPux.png', 'audio' => null],
                    ['image' => 'ebooks/content/o6qShNteK2si6NWfl7W1H0xtcv0HINKze4MBuzN8.png', 'audio' => null],
                    ['image' => 'ebooks/content/BRhz3TdWwM6s6Xdbasqjsn7dJHKeFTPQfn8d9gej.png', 'audio' => null],
                    ['image' => 'ebooks/content/78j58596F3EBoxf0QMqFvmSPwHxJFo1fgwToTK4E.png', 'audio' => null],
                    ['image' => 'ebooks/content/1JDs99Vn8UbPMRa3mHQeNGSrD3O1cAebVnsvYd8F.png', 'audio' => null],
                    ['image' => 'ebooks/content/y34EBPSVV88s7LG5m6S9Egox2bznDvYxs7FKbcK6.png', 'audio' => null],
                ]),
                'thumbnail' => null,
                'ukuran_file' => 22348848,
                'created_at' => '2026-04-24 17:19:15',
                'updated_at' => '2026-04-24 17:19:15',
            ],
            [
                'id' => 3,
                'judul' => 'Belajar Berhitung',
                'deskripsi' => 'Belajar angka dan berhitung dasar dengan metode menyenangkan melalui gambar, animasi, dan aktivitas interaktif untuk anak.',
                'file_path' => json_encode([
                    ['image' => 'ebooks/content/IJ5krlcY7GL9cb2uLvp59H9Wn7dvU2ig8sevENS8.png', 'audio' => null],
                    ['image' => 'ebooks/content/MoVgxWbUghOABxQA0v6KzPosuJe8BhA4vFd6iWjy.png', 'audio' => null],
                    ['image' => 'ebooks/content/YG0zyXMUcV2dv4N7uGnyTLAXIg8GouSSkRNwKWAs.png', 'audio' => null],
                    ['image' => 'ebooks/content/UIbGSjRicr9wn9t8uSBuuC7X0tj4sq9Sbro0BaTR.png', 'audio' => null],
                    ['image' => 'ebooks/content/ljv7AqLFr7Ss4zuVtFEd8clth1ADsbI4ZhQqL8EA.png', 'audio' => null],
                    ['image' => 'ebooks/content/8furYhNYCH4ipzbkL9USMktDczRKRmQvuzQ5XU3D.png', 'audio' => null],
                    ['image' => 'ebooks/content/vwVq9EN80ckED4YW5IdV0s6O41cQS872yyitD8bh.png', 'audio' => null],
                    ['image' => 'ebooks/content/jpHIGmX3Fos2XaGaa0Hpj5LapRAOPg7tLByYGLQj.png', 'audio' => null],
                    ['image' => 'ebooks/content/bnSZA7grdjrCLssm4b6acQRxOrQOMlk6kaEt4xsk.png', 'audio' => null],
                    ['image' => 'ebooks/content/2rKZQ0IemHnN1zsFNbKy5ZXcTKM6ftJobclErBRe.png', 'audio' => null],
                    ['image' => 'ebooks/content/O3wxoQg4Nr7lAnu4QXvQjB1IYt3XMEjm6jIndKH1.png', 'audio' => null],
                    ['image' => 'ebooks/content/FYbJ5k4cnok8zo9t69zJPMxrSnTdlbYF0dyUSjaP.png', 'audio' => null],
                ]),
                'thumbnail' => null,
                'ukuran_file' => 31783092,
                'created_at' => '2026-04-24 17:21:58',
                'updated_at' => '2026-04-24 17:21:58',
            ],
        ];

        foreach ($data as $ebook) {
            Ebook::create($ebook);
        }
    }
}