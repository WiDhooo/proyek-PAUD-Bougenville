<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kegiatan - PAUD Bougenville</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
  </style>
</head>

<body class="bg-[#FFFDF5] text-gray-800">

  <!-- Navbar -->
  <nav class="fixed top-0 left-0 w-full bg-white/90 backdrop-blur-md shadow-sm z-50 transition-all duration-300">
    <div class="container mx-auto px-10 md:px-24 flex justify-between items-center py-4">
      <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition">
        PAUD Bougenville
      </a>
      <ul class="hidden md:flex space-x-8 font-medium">
        <li><a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 transition">Beranda</a></li>
        <li><a href="{{ url('/tentang') }}" class="text-gray-700 hover:text-blue-600 transition">Tentang Kami</a></li>
        <li><a href="{{ url('/kegiatan') }}" class="text-blue-600 font-semibold">Kegiatan</a></li>
        <li><a href="{{ url('/kontak') }}" class="text-gray-700 hover:text-blue-600 transition">Kontak</a></li>
      </ul>
      <a href="#" class="hidden md:inline-block px-5 py-2 border-2 border-blue-500 text-blue-500 font-medium rounded-full hover:bg-blue-500 hover:text-white transition duration-300">
        Mausk
      </a>
    </div>
  </nav>

  <!-- Kegiatan -->
  <section class="pt-32 pb-20 px-10 md:px-24 text-center">
    <h2 class="text-2xl md:text-3xl font-bold mb-3 text-blue-600">
      Dokumentasi <span class="text-[#FF9900]">Kegiatan</span>
    </h2>
    <p class="text-gray-600 mb-12">
      Dokumentasi berbagai kegiatan dan program unggulan yang telah kami selenggarakan.
    </p>

    <!-- Grid Kegiatan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
      <!-- Card Kegiatan -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
        <div class="w-full h-56 bg-gray-200"></div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-blue-600 text-white py-8">
    <div class="container mx-auto px-10 grid grid-cols-1 md:grid-cols-3 gap-8">
      <div>
        <h3 class="font-bold text-lg mb-3">Tentang PAUD Bougenville</h3>
        <p class="text-sm leading-relaxed">
          Lembaga pendidikan anak usia dini yang fokus membangun karakter, kreativitas, dan keceriaan anak-anak dengan penuh kasih sayang.
        </p>
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

    <div class="text-center text-xs text-gray-100 mt-8">
      &copy; 2025 PAUD Bougenville. Semua Hak Dilindungi.
    </div>
  </footer>

</body>
</html>
