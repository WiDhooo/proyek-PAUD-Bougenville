<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - PAUD Bougenville</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Animasi Fade In */
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
                <li><a href="{{ url('/kegiatan') }}" class="relative text-gray-700 hover:text-blue-600 transition duration-300 group">
                    Kegiatan
                    <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                </a></li>
                <li><a href="{{ url('/kontak') }}" class="relative text-gray-700 hover:text-blue-600 transition duration-300 group">
                    Kontak
                    <span class="absolute left-0 -bottom-1 w-0 h-0.5 bg-blue-500 group-hover:w-full transition-all duration-300"></span>
                </a></li>
            </ul>

            <!-- Tombol -->
            <a href="#" class="hidden md:inline-block px-5 py-2 border-2 border-blue-500 text-blue-500 font-medium rounded-full hover:bg-blue-500 hover:text-white transition duration-300">
                Masuk
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
            <a href="{{ url('/kegiatan') }}" class="text-gray-700 hover:text-blue-500">Kegiatan</a>
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

    <!-- Hero Section -->
    <section class="pt-20 md:pt-16">
        <div class="bg-cover bg-center h-screen relative" style="background-image: url('{{ asset('images/bg.png') }}');">
            <div class="absolute inset-0 bg-blue-700 bg-opacity-10"></div>
            <div class="absolute inset-0 flex flex-col justify-center items-start text-left text-white px-10 md:px-24">
                <h1 class="text-3xl md:text-6xl font-bold drop-shadow-lg leading-tight">Selamat Datang di</h1>
                <h1 class="text-3xl md:text-6xl font-bold drop-shadow-lg leading-tight mt-3">PAUD Bougenville</h1>
                <p class="mt-6 text-lg md:text-xl drop-shadow-md">Langkah Kecil Menuju Masa Depan Gemilang</p>
            </div>
        </div>
    </section>

    <!-- Sambutan -->
    <section class="mt-32 px-8 md:px-32 fade-in bg-[#FFFDF5] py-16">
        <h2 class="text-2xl font-bold mb-8 text-blue-500">
            Sambutan <span class="text-[#FF9900]">Kepala Sekolah</span>
        </h2>

        <div class="flex flex-col md:flex-row gap-10 items-start">
            <div class="flex-shrink-0">
                <img 
                    src="{{ asset('images/kepsek.png') }}" 
                    alt="Foto Kepala Sekolah" 
                    class="w-64 h-80 object-cover rounded-lg shadow-lg"
                >
            </div>

            <div class="bg-blue-500 text-white p-8 rounded-lg shadow-md text-justify leading-relaxed flex-1">
                <p>Assalamu’alaikum warahmatullahi wabarakatuh.</p><br>
                <p>
                    Puji syukur kehadirat Allah SWT atas rahmat dan karunia-Nya sehingga kita semua masih diberi kesempatan untuk berperan dalam mendidik generasi penerus bangsa. Selamat datang di website resmi PAUD Bougenville. Website ini kami hadirkan sebagai sarana informasi dan komunikasi antara pihak sekolah, orang tua, dan masyarakat. PAUD Bougenville berkomitmen untuk menciptakan lingkungan belajar yang menyenangkan, penuh kasih sayang, serta menumbuhkan karakter, kreativitas, dan kemandirian anak sejak dini. Melalui website ini, kami berharap masyarakat dapat mengenal lebih dekat visi, misi, dan kegiatan pembelajaran yang kami kembangkan di PAUD Bougenville.
                </p>
            </div>
        </div>
    </section>

    <!-- Program Unggulan -->
    <section class="mt-32 px-8 md:px-32 text-center bg-[#FFFDF5] fade-in py-16">
        <h2 class="text-2xl font-bold mb-3 text-blue-500">
            Program <span class="text-[#FF9900]">Unggulan</span>
        </h2>
        <p class="text-gray-600 mb-10">Dokumentasi berbagai kegiatan dan program unggulan yang telah kami selenggarakan.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-items-center">
            @foreach (['Pesantren Kilat', 'Kajian Islami', 'Buka Puasa Bersama'] as $program)
                <div class="border border-gray-200 shadow-md rounded-lg overflow-hidden w-80 transform hover:scale-105 transition bg-blue-500/10">
                    <img src="{{ asset('images/photo1.png') }}" alt="Program" class="w-full h-48 object-cover">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-blue-600 mb-2">{{ $program }}</h3>
                        <p class="text-sm text-gray-700 mb-4">Kegiatan {{ $program }} bertujuan membentuk karakter anak yang beriman, berakhlak, dan ceria.</p>
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md w-full transition">Selengkapnya</button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white py-10 fade-in">
        <div class="container mx-auto px-10 grid grid-cols-1 md:grid-cols-3 gap-10">
            <div>
                <h3 class="font-bold text-lg mb-3">Tentang PAUD Bougenville</h3>
                <p class="text-sm leading-relaxed">Lembaga pendidikan anak usia dini yang fokus membangun karakter, kreativitas, dan keceriaan anak-anak dengan penuh kasih sayang.</p>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-3">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:underline">Beranda</a></li>
                    <li><a href="{{ url('/tentang') }}" class="hover:underline">Tentang Kami</a></li>
                    <li><a href="{{ url('/kegiatan') }}" class="hover:underline">Kegiatan</a></li>
                    <li><a href="{{ url('/kontak') }}" class="hover:underline">Kontak</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-3">Kontak Kami</h3>
                <p class="text-sm">Alamat: Jl. Pinang Barat, Jakarta Timur</p>
                <p class="text-sm">Email: info@paudbougenville.com</p>
                <p class="text-sm">Telp: +62 812 3456 7890</p>
                <p class="text-sm mt-1">Jam Operasional: Senin - Jumat, 08.00 - 16.00 WIB</p>
            </div>
        </div>

        <div class="text-center text-xs text-gray-100 mt-10">
            &copy; 2025 PAUD Bougenville. Semua Hak Dilindungi.
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
