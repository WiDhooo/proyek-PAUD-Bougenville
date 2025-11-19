<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - PAUD Bougenville</title>
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
                <li><a href="{{ url('/tentang') }}" class="relative text-blue-600 transition duration-300 group">
                    Tentang Kami
                    <span class="absolute left-0 -bottom-1 w-full h-0.5 bg-blue-500 transition-all duration-300"></span>
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
            <a href="{{ url('/tentang') }}" class="text-blue-600">Tentang Kami</a>
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

    <!-- Hero Section Tentang Kami -->
    <section class="pt-20 md:pt-16">
        <div class="bg-cover bg-center h-96 relative" style="background-image: url('{{ asset('images/bg1.png') }}');">
            <div class="absolute inset-0 bg-blue-700 bg-opacity-10"></div>
            <div class="absolute inset-0 flex flex-col justify-center items-start text-left text-white px-10 md:px-24">
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-lg leading-tight">Tentang</h1>
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-lg leading-tight mt-3">PAUD Bougenville</h1>
                <p class="mt-6 text-lg md:text-xl drop-shadow-md">Mengenal lebih dekat visi, misi, dan komitmen kami</p>
            </div>
        </div>
    </section>

    <!-- Tentang Kami -->
    <section class="py-16 px-8 md:px-32 fade-in bg-[#FFFDF5]">
        <h2 class="text-2xl font-bold mb-8 text-blue-500 text-center">
            Tentang <span class="text-[#FF9900]">Kami</span>
        </h2>

        <div class="flex flex-col md:flex-row gap-10 items-start max-w-6xl mx-auto">
            <div class="flex-shrink-0 mx-auto md:mx-0">
                <img 
                    src="{{ asset('images/gbr4.jpg') }}" 
                    alt="Gedung PAUD Bougenville" 
                    class="w-80 h-110 object-cover rounded-lg shadow-lg"
                >
            </div>

            <div class="bg-blue-500 text-white p-8 rounded-lg shadow-md text-justify leading-relaxed flex-1">
                <p class="font-semibold text-lg mb-4">Selamat Datang di PAUD Bougenville</p>
                <p class="mb-4">
                    {!! nl2br(e($profil->tentang_sekolah ?? 'Informasi belum tersedia.')) !!}
                </p>
            </div>
        </div>
    </section>

    <!-- Visi Misi -->
    <section class="py-16 bg-white fade-in">
        <div class="container mx-auto px-8 md:px-32">
            <h2 class="text-2xl font-bold mb-3 text-blue-500 text-center">
                Visi & <span class="text-[#FF9900]">Misi</span>
            </h2>
            <p class="text-gray-600 mb-10 text-center max-w-2xl mx-auto">Pedoman kami dalam mendidik generasi penerus bangsa</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Visi -->
                <div class="bg-blue-500 text-white p-8 rounded-lg shadow-md">
                    <h3 class="font-semibold text-lg mb-4 text-center">Visi</h3>
                    @if($visi->count() > 1)
                        <ul class="list-disc list-inside space-y-2 text-sm leading-relaxed">
                            @foreach($visi as $item)
                                <li>{{ $item->isi }}</li>
                            @endforeach
                        </ul>
                    @elseif($visi->count() === 1)
                        <p class="text-center leading-relaxed">{{ $visi->first()->isi }}</p>
                    @else
                        <p class="text-center leading-relaxed text-blue-100">Visi belum diatur</p>
                    @endif
                </div>

                <!-- Misi -->
                <div class="bg-blue-500 text-white p-8 rounded-lg shadow-md">
                    <h3 class="font-semibold text-lg mb-4 text-center">Misi</h3>
                    @if($misi->count() > 1)
                        <ul class="list-disc list-inside space-y-2 text-sm leading-relaxed">
                            @foreach($misi as $item)
                                <li>{{ $item->isi }}</li>
                            @endforeach
                        </ul>
                    @elseif($misi->count() === 1)
                        <p class="text-center leading-relaxed">{{ $misi->first()->isi }}</p>
                    @else
                        <p class="text-center leading-relaxed text-blue-100">Misi belum diatur</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Staf Pengajar -->
    <section class="py-16 bg-[#FFFDF5] fade-in">
        <div class="container mx-auto px-8 md:px-32">
            <h2 class="text-2xl font-bold mb-3 text-blue-500 text-center">
                Staf <span class="text-[#FF9900]">Pengajar</span>
            </h2>
            <p class="text-gray-600 mb-10 text-center max-w-2xl mx-auto">Guru-guru berpengalaman yang siap mendampingi tumbuh kembang anak Anda</p>

            <!-- Card Kepala Sekolah (Compact Design) -->
<div class="flex justify-center mb-12">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-2xl transform hover:scale-[1.02] transition-all duration-300 group border border-blue-100">
        <div class="flex flex-col lg:flex-row">
            <!-- Foto Section -->
            <div class="lg:w-2/5 relative overflow-hidden bg-gradient-to-br from-blue-50 to-orange-50">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-orange-400/10 z-10"></div>
                <img 
                    src="{{ asset('images/1.png') }}" 
                    alt="Ibu Endang Sulistiawati S.Pd - Kepala Sekolah"
                    class="w-full h-48 lg:h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500"
                >
                <!-- Decorative Elements -->
                <div class="absolute top-3 left-3 w-6 h-6 border-2 border-white rounded-full"></div>
                <div class="absolute bottom-3 right-3 w-4 h-4 border-2 border-white rounded-full"></div>
            </div>
            
            <!-- Content Section -->
            <div class="lg:w-3/5 p-6">
                <div class="flex items-center mb-3">
                    <div class="w-2 h-6 bg-orange-400 rounded-full mr-2"></div>
                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Kepala Sekolah</span>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-1">Ibu Endang Sulistiawati S.Pd.</h3>
                <p class="text-blue-500 text-sm mb-3">Memimpin dengan Hati dan Dedikasi</p>
                
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    Dengan pengalaman lebih dari 10 tahun di bidang pendidikan anak usia dini, 
                    berkomitmen menciptakan lingkungan belajar yang aman, nyaman, dan penuh 
                    kasih sayang bagi setiap anak.
                </p>
                
                <div class="flex flex-wrap gap-1 mb-4">
                    <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-full text-xs">Pendidikan Karakter</span>
                    <span class="px-2 py-1 bg-orange-100 text-orange-600 rounded-full text-xs">Manajemen</span>
                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs">Psikologi Anak</span>
                </div>
                
                <div class="flex space-x-3">
                    <div class="text-center">
                        <div class="text-base font-bold text-blue-600">10+</div>
                        <div class="text-xs text-gray-500">Tahun</div>
                    </div>
                    <div class="text-center">
                        <div class="text-base font-bold text-orange-500">80+</div>
                        <div class="text-xs text-gray-500">Siswa</div>
                    </div>
                    <div class="text-center">
                        <div class="text-base font-bold text-green-500">25+</div>
                        <div class="text-xs text-gray-500">Pelatihan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            <!-- Card Guru dengan Foto Bulat Besar -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ([
                    ['nama' => 'Bu Ecin Kuraesin, S.Pd', 'jabatan' => 'Bendahara', 'foto' => '2.png'],
                    ['nama' => 'Bu Wiwin Charyani', 'jabatan' => 'Sekretaris/Tenaga Pendidik', 'foto' => '3.png'],
                    ['nama' => 'Bu Sukarsih', 'jabatan' => 'Tenaga Pendidik', 'foto' => '4.png'],
                    ['nama' => 'Bu Yeany Maritha, S.Pd', 'jabatan' => 'Tenaga Pendidik', 'foto' => '5.png'],
                    ['nama' => 'Bu Kowiyah', 'jabatan' => 'Tenaga Pendidik', 'foto' => '6.png'],
                    ['nama' => 'Bu Nina Yuanti', 'jabatan' => 'Tenaga Pendidik', 'foto' => '7.png']
                ] as $guru)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300 group hover:shadow-xl border border-gray-100">
                        <!-- Header dengan Foto Bulat Besar -->
                        <div class="relative h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center pt-8">
                            <!-- Background Pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-4 left-4 w-8 h-8 border-2 border-white rounded-full"></div>
                                <div class="absolute bottom-4 right-4 w-6 h-6 border-2 border-white rounded-full"></div>
                                <div class="absolute top-1/2 left-1/4 w-4 h-4 border-2 border-white rounded-full"></div>
                            </div>
                            
                            <!-- Foto Bulat Besar -->
                            <div class="relative z-10">
                                <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden transform group-hover:scale-110 transition-transform duration-300">
                                    <img 
                                        src="{{ asset('images/' . $guru['foto']) }}" 
                                        alt="{{ $guru['nama'] }}"
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6 text-center">
                            <!-- Nama dan Jabatan -->
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $guru['nama'] }}</h3>
                                <p class="text-blue-500 font-medium">{{ $guru['jabatan'] }}</p>
                            </div>
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