<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ebook->judul }} - PAUD Bougenville</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-[#FFFDF5] text-gray-800">

    <nav class="fixed top-0 left-0 w-full bg-white/90 backdrop-blur-md shadow-xl z-50 transition-all duration-300">
        <div class="container mx-auto px-6 md:px-24 flex justify-between items-center py-4">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition">
                PAUD Bougenville
            </a>

            <ul class="hidden md:flex space-x-8 font-medium">
                <li><a href="{{ url('/') }}" class="relative text-blue-600 transition duration-300 group">
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

            <a href="{{ route('login') }}" class="hidden md:inline-block px-5 py-2 border-2 border-blue-500 text-blue-500 font-medium rounded-full hover:bg-blue-500 hover:text-white transition duration-300">
                Portal Admin
            </a>

            <button id="menu-btn" class="md:hidden flex flex-col space-y-1">
                <span class="w-6 h-0.5 bg-gray-700"></span>
                <span class="w-6 h-0.5 bg-gray-700"></span>
                <span class="w-6 h-0.5 bg-gray-700"></span>
            </button>
        </div>

        <div id="mobile-menu" class="hidden flex-col items-center space-y-4 bg-white shadow-md py-6 md:hidden">
            <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-500">Beranda</a>
            <a href="{{ url('/tentang') }}" class="text-gray-700 hover:text-blue-500">Tentang Kami</a>
            <a href="{{ url('/kegiatan') }}" class="text-gray-700 hover:text-blue-500">Kegiatan</a>
            <a href="{{ url('/kontak') }}" class="text-gray-700 hover:text-blue-500">Kontak</a>
            <a href="{{ route('login') }}" class="px-5 py-2 border-2 border-blue-500 text-blue-500 rounded-full">Portal Admin</a>
        </div>
    </nav>

    <section class="pt-32 pb-20 px-6 md:px-24">
        <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="p-8 md:p-16">
                <div class="flex flex-col md:flex-row gap-12 items-start">
                    
                    <div class="w-full md:w-1/3 flex flex-col items-center">
                        <div class="w-full shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
                            @php
                                // Amankan data file_path
                                $pages = $ebook->file_path;
                                if (is_string($pages)) {
                                    $pages = json_decode($pages, true);
                                }

                                // Logika Cover: Thumbnail > Halaman 1 > Default
                                if ($ebook->thumbnail) {
                                    $cover = asset('storage/' . $ebook->thumbnail);
                                } elseif (is_array($pages) && count($pages) > 0) {
                                    $cover = asset('storage/' . $pages[0]);
                                } else {
                                    $cover = asset('images/default-ebook.png');
                                }
                            @endphp
                            {{-- onerror ditambahkan sebagai perlindungan tambahan jika file fisik hilang --}}
                            <img src="{{ $cover }}" alt="{{ $ebook->judul }}" 
                                 class="w-full aspect-[3/4] object-cover shadow-inner"
                                 onerror="this.onerror=null;this.src='{{ asset('images/default-ebook.png') }}';">
                        </div>
                    </div>

                    <div class="w-full md:w-2/3">
                        <h1 class="text-3xl md:text-5xl font-bold text-[#1E293B] mb-2">{{ $ebook->judul }}</h1>
                        <div class="flex items-center gap-2 mb-8 text-sm">
                            <span class="text-gray-500">Jumlah</span>
                            <span class="font-medium">: 
                                {{ is_array($pages) ? count($pages) : 0 }} Halaman
                            </span>
                        </div>

                        <div class="mb-10">
                            <h3 class="font-bold text-xl text-[#1E293B] mb-4">Sinopsis :</h3>
                            <p class="text-gray-600 leading-relaxed text-justify text-lg">
                                {{ $ebook->deskripsi ?? 'Belum ada deskripsi untuk materi ini.' }}
                            </p>
                        </div>

                        <div class="flex">
                            <a href="{{ route('ebook.read', $ebook->id) }}" class="bg-[#E54350] hover:bg-red-700 text-white px-10 py-4 rounded-xl font-bold flex items-center gap-3 transition shadow-lg transform hover:scale-105">
                                <i class="fas fa-book-open"></i> BACA SEKARANG
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Toggle menu mobile
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if(menuBtn) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('flex');
            });
        }

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