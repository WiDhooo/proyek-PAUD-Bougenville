<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kegiatan - PAUD Bougenville</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease-out;
        }

        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-[#FFFDF5] text-gray-800">


    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full bg-white/90 backdrop-blur-md shadow-sm z-50 transition-all duration-300">
        <div class="container mx-auto px-6 md:px-24 flex justify-between items-center py-4">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition">
                PAUD Bougenville
            </a>

            <!-- Menu Desktop -->
            <ul class="hidden md:flex space-x-8 font-medium">
                <li><a href="{{ url('/') }}" class="relative text-gray-700 hover:text-blue-600 transition duration-300 group">
                    Beranda
                    <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                </a></li>
                <li><a href="{{ url('/tentang') }}" class="relative text-gray-700 hover:text-blue-600 transition duration-300 group">
                    Tentang Kami
                    <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                </a></li>
                <li><a href="{{ url('/kegiatan') }}" class="relative text-blue-600 transition duration-300 group">
                    Kegiatan
                    <span class="absolute left-0 -bottom-1 w-full h-0.5 bg-blue-500 transition-all duration-300"></span>
                </a></li>
                <li><a href="{{ url('/kontak') }}" class="relative text-gray-700 hover:text-blue-600 transition duration-300 group">
                    Kontak
                    <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                </a></li>
            </ul>

            <!-- Tombol -->
            <a href="#" class="hidden md:inline-block px-5 py-2 border-2 border-blue-500 text-blue-500 font-medium rounded-full hover:bg-blue-500 hover:text-white transition duration-300">
                Daftar Sekarang
            </a>

            <!-- Hamburger -->
            <button id="menu-btn" class="md:hidden flex flex-col space-y-1">
                <span class="w-6 h-0.5 bg-gray-700"></span>
                <span class="w-6 h-0.5 bg-gray-700"></span>
                <span class="w-6 h-0.5 bg-gray-700"></span>
            </button>
        </div>

        <!-- Menu Mobile -->
        <div id="mobile-menu" class="hidden flex-col items-center space-y-4 bg-white shadow-md py-6 md:hidden">
            <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-500">Beranda</a>
            <a href="{{ url('/tentang') }}" class="text-gray-700 hover:text-blue-500">Tentang Kami</a>
            <a href="{{ url('/kegiatan') }}" class="text-blue-600">Kegiatan</a>
            <a href="{{ url('/kontak') }}" class="text-gray-700 hover:text-blue-500">Kontak</a>
            <a href="#" class="px-4 py-2 border-2 border-blue-500 text-blue-500 rounded-full hover:bg-blue-500 hover:text-white transition">
                Daftar Sekarang
            </a>
        </div>
    </nav>

    <script>
        // Toggle menu mobile
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
        });
    </script>

    <!-- Hero Section Kegiatan -->
    <section class="pt-20 md:pt-16">
        <div class="bg-cover bg-center h-96 relative" style="background-image: url('{{ asset('images/bg1.png') }}');">
            <div class="absolute inset-0 bg-blue-700 bg-opacity-10"></div>
            <div class="absolute inset-0 flex flex-col justify-center items-start text-left text-white px-6 md:px-24">
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-lg leading-tight">Kegiatan</h1>
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-lg leading-tight mt-3">PAUD Bougenville</h1>
                <p class="mt-6 text-lg md:text-xl drop-shadow-md max-w-2xl">Dokumentasi berbagai kegiatan dan program unggulan</p>
            </div>
        </div>
    </section>

    <!-- Galeri Kegiatan -->
    <section class="py-16 bg-[#FFFDF5] fade-in">
        <div class="container mx-auto px-6 md:px-8 lg:px-32">
            <h2 class="text-2xl font-bold mb-3 text-blue-500 text-center">
                Galeri <span class="text-[#FF9900]">Kegiatan</span>
            </h2>
            <p class="text-gray-600 mb-10 text-center max-w-2xl mx-auto px-4">Dokumentasi berbagai kegiatan dan momen berharga di PAUD Bougenville</p>

            <!-- Grid Kegiatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 px-4 sm:px-0">
                @foreach ([
                    ['img' => 'gbr1.png', 'title' => 'Melatih Kreativitas Anak', 
                    'desc' => 'Anak-anak diajak mengekspresikan ide dan imajinasi melalui berbagai kegiatan seni seperti membuat kerajinan.'],

                    ['img' => 'gbr2.png', 'title' => 'Makan Siang Bersama', 
                    'desc' => 'Suasana makan siang yang menyenangkan, di mana anak-anak belajar makan mandiri bersama teman-teman.'],

                    ['img' => 'gbr3.png', 'title' => 'Latihan Marching Band', 
                    'desc' => 'Melatih koordinasi, motorik, serta kekompakan anak melalui latihan musik  marching band dan gerak ritmis.'],

                    ['img' => 'gbr3.jpg', 'title' => 'Sarapan Bersama Teman', 
                    'desc' => 'Menumbuhkan kebiasaan hidup sehat serta kemampuan sosial anak melalui kegiatan sarapan bersama teman-teman.'],

                    ['img' => 'gbr1.jpg', 'title' => 'Persiapan Mengikuti Pawai', 
                    'desc' => 'Foto bersama sebelum melakukan pawai, anak-anak belajar bekerja sama dan menyiapkan atribut pawai.'],

                    ['img' => 'gbr2.jpg', 'title' => 'Kegiatan Olahraga Bersama', 
                    'desc' => 'Kegiatan olahraga yang dilakukan setiap hari jumat untuk memperkuat tubuh, dan rasa kebersamaan.']
                    ] as $kegiatan)
                    <div class="bg-white border border-gray-200 shadow-md rounded-lg overflow-hidden transform hover:scale-105 transition-all duration-300 group cursor-pointer fade-in">
                        <div class="relative overflow-hidden">
                            <img src="{{ asset('images/' . $kegiatan['img']) }}" alt="{{ $kegiatan['title'] }}" class="w-full h-48 md:h-56 object-cover group-hover:scale-110 transition duration-500">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition duration-300"></div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-blue-600 mb-2 group-hover:text-blue-700 transition">{{ $kegiatan['title'] }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $kegiatan['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

<!-- Footer -->
<footer class="bg-blue-600 text-white py-12 fade-in">
    <div class="container mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            <!-- Tentang -->
            <div class="space-y-4 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start mb-4">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-school text-white text-sm"></i>
                    </div>
                    <h3 class="font-bold text-lg">Tentang PAUD Bougenville</h3>
                </div>
                <p class="text-blue-100 leading-relaxed text-sm max-w-md mx-auto md:mx-0">
                    Lembaga pendidikan anak usia dini yang fokus membangun karakter, kreativitas, dan keceriaan anak-anak dengan penuh kasih sayang.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start mb-4">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-link text-white text-sm"></i>
                    </div>
                    <h3 class="font-bold text-lg">Quick Links</h3>
                </div>
                <ul class="space-y-3">
                    <li class="flex justify-center md:justify-start">
                        <a href="{{ url('/') }}" class="text-blue-100 hover:text-white transition duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                            Beranda
                        </a>
                    </li>
                    <li class="flex justify-center md:justify-start">
                        <a href="{{ url('/tentang') }}" class="text-blue-100 hover:text-white transition duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                            Tentang Kami
                        </a>
                    </li>
                    <li class="flex justify-center md:justify-start">
                        <a href="{{ url('/kegiatan') }}" class="text-blue-100 hover:text-white transition duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                            Kegiatan
                        </a>
                    </li>
                    <li class="flex justify-center md:justify-start">
                        <a href="{{ url('/kontak') }}" class="text-blue-100 hover:text-white transition duration-300 flex items-center group">
                            <i class="fas fa-chevron-right text-xs mr-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                            Kontak
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Kontak -->
            <div class="space-y-4 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start mb-4">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-phone text-white text-sm"></i>
                    </div>
                    <h3 class="font-bold text-lg">Kontak Kami</h3>
                </div>
                <div class="space-y-3 text-blue-100">
                    <div class="flex flex-col items-center md:items-start text-center md:text-left">
                        <div class="flex items-start mb-2">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-sm w-4"></i>
                            <span class="text-sm">Jl. Kelapa Sawit V Kelapa Rt 03 Rw 10<br>Kel. Utan Kayu Selatan, Matraman<br>Jakarta Timur (13120)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-envelope mr-3 text-sm w-4"></i>
                        <span class="text-sm font-medium">bougenvilleuks@gmail.com</span>
                    </div>
                    <div class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-phone mr-3 text-sm w-4"></i>
                        <span class="text-sm font-medium">081513747681</span>
                    </div>
                    <div class="flex items-center justify-center md:justify-start">
                        <i class="fas fa-clock mt-1 mr-3 text-sm w-4"></i>
                        <span class="text-sm">Senin - Jumat, 08.00 - 16.00 WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-blue-500/30 mt-8 pt-8 text-center">
            <div class="text-blue-100 text-sm">
                &copy; 2025 PAUD Bougenville. Semua Hak Dilindungi.
            </div>
        </div>
    </div>
</footer>

    <script>
        // Animasi scroll
        const elements = document.querySelectorAll('.fade-in');
        const showOnScroll = () => {
            elements.forEach(el => {
                const rect = el.getBoundingClientRect();
                if (rect.top < window.innerHeight - 100) el.classList.add('show');
            });
        };
        window.addEventListener('scroll', showOnScroll);
        window.addEventListener('load', showOnScroll);
    </script>

</body>
</html>