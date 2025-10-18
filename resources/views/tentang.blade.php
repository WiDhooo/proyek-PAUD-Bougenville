<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tentang Kami - PAUD Bougenville</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
        <li><a href="{{ url('/tentang') }}" class="text-blue-600 font-semibold">Tentang Kami</a></li>
        <li><a href="{{ url('/kegiatan') }}" class="text-gray-700 hover:text-blue-600 transition">Kegiatan</a></li>
        <li><a href="{{ url('/kontak') }}" class="text-gray-700 hover:text-blue-600 transition">Kontak</a></li>
      </ul>
      <a href="#" class="hidden md:inline-block px-5 py-2 border-2 border-blue-500 text-blue-500 font-medium rounded-full hover:bg-blue-500 hover:text-white transition duration-300">
        Daftar Sekarang
      </a>
    </div>
  </nav>

  <!-- Tentang Kami -->
  <section class="pt-32 px-10 md:px-24">
    <div class="flex flex-col md:flex-row gap-10 items-start">
      <!-- Teks -->
      <div class="flex-1" data-aos="fade-right">
        <h2 class="text-2xl font-bold text-blue-600 mb-3">
          Tentang <span class="text-[#FF9900]">Kami</span>
        </h2>
        <p class="text-gray-700 leading-relaxed text-justify">
          PAUD Bougenville adalah lembaga pendidikan anak usia dini yang berkomitmen untuk menciptakan lingkungan belajar
          yang menyenangkan, aman, dan penuh kasih sayang. Berdiri dengan semangat mencerdaskan generasi penerus bangsa,
          kami percaya bahwa setiap anak memiliki potensi luar biasa yang perlu dikembangkan sejak dini melalui pendidikan
          yang tepat dan menyenangkan.
        </p>
      </div>
      <!-- Foto -->
      <div class="grid grid-cols-2 gap-4 flex-1" data-aos="zoom-in">
        <div class="w-full h-36 bg-gray-200 rounded-lg"></div>
        <div class="w-full h-36 bg-gray-200 rounded-lg"></div>
        <div class="w-full h-36 bg-gray-200 rounded-lg"></div>
        <div class="w-full h-36 bg-gray-200 rounded-lg"></div>
      </div>
    </div>
  </section>

  <!-- Visi Misi -->
  <section class="mt-20 px-10 md:px-24" data-aos="fade-up">
    <h2 class="text-2xl font-bold text-center text-blue-600 mb-10">Visi Misi</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
      <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md" data-aos="fade-right">
        <h3 class="font-semibold text-lg mb-2">Visi</h3>
        <p>Menumbuhkan generasi yang beriman, berakhlak mulia, kreatif, dan mandiri.</p>
      </div>
      <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md" data-aos="fade-left">
        <h3 class="font-semibold text-lg mb-2">Misi</h3>
        <ul class="list-disc list-inside space-y-1">
          <li>Menyelenggarakan pembelajaran berbasis bermain dan pengalaman langsung.</li>
          <li>Menanamkan nilai agama dan moral sejak dini.</li>
          <li>Mengembangkan potensi anak melalui kegiatan kreatif dan inovatif.</li>
          <li>Menjalin kerja sama antara sekolah, orang tua, dan masyarakat.</li>
          <li>Menciptakan lingkungan belajar yang aman, bersih, dan ramah anak.</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Staf Pengajar -->
  <section class="mt-20 mb-16 px-10 md:px-24" data-aos="fade-up">
    <h2 class="text-2xl font-bold text-center text-[#FF9900] mb-10">Staf Pengajar</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 justify-items-center">
      @foreach (['Bu Rina', 'Bu Siti', 'Bu Wati', 'Bu Nur', 'Bu Dewi', 'Bu Lilis'] as $nama)
        <div class="bg-blue-500 text-white rounded-lg shadow-md w-64 h-40 flex flex-col justify-center items-center hover:scale-105 transition" data-aos="zoom-in">
          <div class="w-20 h-20 bg-white rounded-full mb-3"></div>
          <h3 class="font-semibold">{{ $nama }}</h3>
          <p class="text-sm opacity-90">Guru PAUD</p>
        </div>
      @endforeach
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-blue-600 text-white py-8" data-aos="fade-up">
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

  <!-- Script AOS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>

</body>
</html>
