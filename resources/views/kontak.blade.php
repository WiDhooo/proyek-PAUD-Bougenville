<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - PAUD Bougenville</title>
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
                <li><a href="{{ url('/kontak') }}" class="relative text-blue-600 transition duration-300 group">
                    Kontak
                    <span class="absolute left-0 -bottom-1 w-full h-0.5 bg-blue-500 transition-all duration-300"></span>
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
            <a href="{{ url('/kegiatan') }}" class="text-gray-700 hover:text-blue-500">Kegiatan</a>
            <a href="{{ url('/kontak') }}" class="text-blue-600">Kontak</a>
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

    <!-- Hero Section Kontak -->
    <section class="pt-20 md:pt-16">
        <div class="bg-cover bg-center h-96 relative" style="background-image: url('{{ asset('images/bg1.png') }}');">
            <div class="absolute inset-0 bg-blue-700 bg-opacity-10"></div>
            <div class="absolute inset-0 flex flex-col justify-center items-start text-left text-white px-10 md:px-24">
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-lg leading-tight">Kontak</h1>
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-lg leading-tight mt-3">PAUD Bougenville</h1>
                <p class="mt-6 text-lg md:text-xl drop-shadow-md">Hubungi kami untuk informasi lebih lanjut</p>
            </div>
        </div>
    </section>

    <!-- Info Kontak -->
    <section class="py-16 bg-white fade-in">
        <div class="container mx-auto px-8 md:px-32">
            <h2 class="text-2xl font-bold mb-3 text-blue-500 text-center">
                Informasi <span class="text-[#FF9900]">Kontak</span>
            </h2>
            <p class="text-gray-600 mb-10 text-center max-w-2xl mx-auto">Silakan hubungi kami untuk informasi pendaftaran, kunjungan, atau pertanyaan lainnya</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#FFFDF5] p-6 rounded-lg shadow-md border border-gray-100 text-center transform hover:scale-105 transition duration-300">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-600 mb-3">Telepon</h3>
                    <p class="text-gray-600 text-sm mb-2 font-medium">081513747681</p>
                    <p class="text-gray-500 text-xs">Senin - Jumat, 08.00 - 16.00 WIB</p>
                </div>

                <div class="bg-[#FFFDF5] p-6 rounded-lg shadow-md border border-gray-100 text-center transform hover:scale-105 transition duration-300">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-green-600 mb-3">Email</h3>
                    <p class="text-gray-600 text-sm font-medium">bougenvilleuks@gmail.com</p>
                </div>

                <div class="bg-[#FFFDF5] p-6 rounded-lg shadow-md border border-gray-100 text-center transform hover:scale-105 transition duration-300">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-orange-600 mb-3">Alamat</h3>
                    <p class="text-gray-600 text-sm">Jl. Kelapa Sawit V Kelapa<br>Kel. Utan Kayu Selatan, Matraman<br>Jakarta Timur (13120)</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Kontak & Map -->
    <section class="py-16 bg-[#FFFDF5] fade-in">
        <div class="container mx-auto px-8 md:px-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Form Kontak -->
                <div>
                    <h2 class="text-2xl font-bold mb-3 text-blue-500">
                        Kirim <span class="text-[#FF9900]">Pesan</span>
                    </h2>
                    <p class="text-gray-600 mb-6">Isi form berikut untuk mengirim pesan kepada kami</p>
                    
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" placeholder="Masukkan nama lengkap">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                                <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" placeholder="Masukkan email">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Subjek</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" placeholder="Subjek pesan">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Pesan</label>
                            <textarea rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300" placeholder="Tulis pesan Anda di sini..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-600 transition duration-300 transform hover:-translate-y-1">
                            Kirim Pesan
                        </button>
                    </form>
                </div>

                <!-- Google Maps -->
                <div>
                    <h2 class="text-2xl font-bold mb-3 text-blue-500">
                        Lokasi <span class="text-[#FF9900]">Kami</span>
                    </h2>
                    <p class="text-gray-600 mb-6">Kunjungi lokasi PAUD Bougenville di alamat berikut</p>
                    
                    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m12!1m8!1m3!1d63462.634054902526!2d106.8676109!3d-6.2089159!3m2!1i1024!2i768!4f13.1!2m1!1ssekretariat%20rw%2010%20utan%20kayu%20selatan!5e0!3m2!1sen!2sid!4v1761732823672!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" class="rounded-lg">
                            </iframe>
                        </div>
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-blue-500 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-blue-600">Alamat Lengkap</h4>
                                    <p class="text-gray-600 text-sm">
                                        Jl. Kelapa Sawit V Kelapa Rt 03 Rw 10<br>
                                        Kel. Utan Kayu Selatan, Matraman<br>
                                        Jakarta Timur (13120)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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