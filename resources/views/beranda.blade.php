<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - PAUD Bougenville</title>
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
            <a href="https://wa.me/6281513747681?text=Halo,%20saya%20ingin%20mendaftar%20di%20PAUD%20Bougenville" class="px-4 py-2 border-2 border-blue-500 text-blue-500 rounded-full hover:bg-blue-500 hover:text-white transition">
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
                <div class="mt-8 flex gap-4">
                   <a href="{{ url('/tentang') }}" class="border-2 border-white text-white px-6 py-3 rounded-full font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                        Jelajahi Sekolah
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Prestasi -->
    <section class="py-16 bg-white fade-in">
        <div class="container mx-auto px-8 md:px-32">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-6">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">80+</div>
                    <div class="text-gray-600">Siswa Aktif</div>
                </div>
                <div class="p-6">
                    <div class="text-3xl md:text-4xl font-bold text-orange-500 mb-2">7+</div>
                    <div class="text-gray-600">Guru Berpengalaman</div>
                </div>
                <div class="p-6">
                    <div class="text-3xl md:text-4xl font-bold text-green-500 mb-2">3</div>
                    <div class="text-gray-600">Kelas Unggulan</div>
                </div>
                <div class="p-6">
                    <div class="text-3xl md:text-4xl font-bold text-purple-500 mb-2">10+</div>
                    <div class="text-gray-600">Tahun Berpengalaman</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sambutan -->
    <section class="py-16 px-8 md:px-32 fade-in bg-[#FFFDF5]">
        <h2 class="text-2xl font-bold mb-8 text-blue-500 text-center">
            Sambutan <span class="text-[#FF9900]">Kepala Sekolah</span>
        </h2>

        <div class="flex flex-col md:flex-row gap-10 items-start max-w-6xl mx-auto">
            <div class="flex-shrink-0 mx-auto md:mx-0">
                <img 
                    src="{{ asset('images/1.png') }}" 
                    alt="Foto Kepala Sekolah" 
                    class="w-64 h-80 object-cover rounded-lg shadow-lg"
                >
            </div>

            <div class="bg-blue-500 text-white p-8 rounded-lg shadow-md text-justify leading-relaxed flex-1">
                <p class="font-semibold text-lg mb-4">Assalamu'alaikum warahmatullahi wabarakatuh</p>
                <p class="mb-4">
                    Puji syukur kehadirat Allah SWT atas rahmat dan karunia-Nya sehingga kita semua masih diberi kesempatan untuk berperan dalam mendidik generasi penerus bangsa. Selamat datang di website resmi PAUD Bougenville.
                </p>
                <p>
                    Website ini kami hadirkan sebagai sarana informasi dan komunikasi antara pihak sekolah, orang tua, dan masyarakat. PAUD Bougenville berkomitmen untuk menciptakan lingkungan belajar yang menyenangkan, penuh kasih sayang, serta menumbuhkan karakter, kreativitas, dan kemandirian anak sejak dini.
                </p>
            </div>
        </div>
    </section>

    <!-- Program Unggulan -->
    <section class="py-16 bg-white fade-in">
        <div class="container mx-auto px-8 md:px-32">
            <h2 class="text-2xl font-bold mb-3 text-blue-500 text-center">
                Program <span class="text-[#FF9900]">Unggulan</span>
            </h2>
            <p class="text-gray-600 mb-10 text-center max-w-2xl mx-auto">Program pembelajaran untuk mengoptimalkan potensi dan bakat setiap anak</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#FFFDF5] p-6 rounded-lg shadow-md border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-palette text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-600 mb-3">Kreativitas Seni</h3>
                    <p class="text-gray-600 text-sm">Mengembangkan imajinasi dan ekspresi melalui berbagai media seni dan kerajinan</p>
                </div>

                <div class="bg-[#FFFDF5] p-6 rounded-lg shadow-md border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-seedling text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-green-600 mb-3">Eksplorasi Alam</h3>
                    <p class="text-gray-600 text-sm">Belajar mengenal lingkungan dan alam sekitar melalui pengalaman langsung</p>
                </div>

                <div class="bg-[#FFFDF5] p-6 rounded-lg shadow-md border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-orange-600 mb-3">Literasi Dini</h3>
                    <p class="text-gray-600 text-sm">Membangun fondasi membaca dan berhitung dengan metode yang menyenangkan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ruang Belajar -->
    <section class="py-16 px-8 md:px-32 text-center bg-[#FFFDF5] fade-in">
        <h2 class="text-2xl font-bold mb-3 text-blue-500">
            Ruang<span class="text-[#FF9900]"> Belajar</span>
        </h2>
        <p class="text-gray-600 mb-10">Ruang belajar yang nyaman dan menyenangkan untuk mendukung petualangan belajar si kecil.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-items-center">
            @foreach ([
                ['title' => 'Ruang Belajar Edukatif', 'image' => 'ruangbelajar.png', 'color' => 'blue', 'icon' => 'fa-gamepad'],
                ['title' => 'Rak Buku Warna-Warni', 'image' => 'rak.jpeg', 'color' => 'green', 'icon' => 'fa-book'],
                ['title' => 'Pojok Literasi', 'image' => 'literasi.jpeg', 'color' => 'orange', 'icon' => 'fa-readme']
            ] as $item)
                <div class="bg-white border-l-4 border-{{ $item['color'] }}-400 shadow-lg rounded-lg overflow-hidden w-80 transform hover:scale-105 hover:shadow-xl transition-all duration-300 group cursor-pointer">
                    <div class="relative">
                        <img src="{{ asset('images/' . $item['image']) }}" alt="{{ $item['title'] }}" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-4 left-4 w-10 h-10 bg-{{ $item['color'] }}-500 rounded-full flex items-center justify-center">
                            <i class="fas {{ $item['icon'] }} text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 group-hover:text-{{ $item['color'] }}-600 transition">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            @if($item['title'] == 'Ruang Belajar Edukatif')
                                Area belajar edukatif untuk mengembangkan motorik dan kreativitas anak.
                            @elseif($item['title'] == 'Rak Buku Warna-Warni')
                                Rak buku cerita dengan desain warna cerah, membantu menumbuhkan minat baca anak sejak dini dengan suasana yang menyenangkan.
                            @else
                                Pojok Literasi adalah tempat baca yang nyaman, di mana anak-anak bisa mengenal banyak cerita seru dan belajar mencintai buku sejak dini.
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
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