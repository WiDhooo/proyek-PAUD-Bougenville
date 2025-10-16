<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kontak - PAUD Bougenville</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }

    /* Animasi Fade-In */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }
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
        <li><a href="{{ url('/kegiatan') }}" class="text-gray-700 hover:text-blue-600 transition">Kegiatan</a></li>
        <li><a href="{{ url('/kontak') }}" class="text-blue-600 font-semibold">Kontak</a></li>
      </ul>
      <a href="#" class="hidden md:inline-block px-5 py-2 border-2 border-blue-500 text-blue-500 font-medium rounded-full hover:bg-blue-500 hover:text-white transition duration-300">
        Daftar Sekarang
      </a>
    </div>
  </nav>

  <!-- Kontak Section -->
  <section class="pt-32 pb-16 px-10 md:px-24 text-center fade-in">
    <h2 class="text-2xl md:text-3xl font-bold text-blue-600 mb-3">Kontak</h2>
    <p class="text-gray-600 mb-12">
      Silahkan menghubungi kami untuk menyampaikan pertanyaan, komentar, saran, maupun hal lainnya.
    </p>

    <!-- Info Kontak -->
    <div class="flex flex-col md:flex-row justify-center gap-6 mb-12 fade-in">
      <div class="bg-blue-500 text-white w-full md:w-1/3 py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition duration-500 hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0-1.242 1.008-2.25 2.25-2.25h2.25a2.25 2.25 0 012.25 2.25v1.5a2.25 2.25 0 01-2.25 2.25H4.5v2.25c0 3.728 3.022 6.75 6.75 6.75h2.25a2.25 2.25 0 012.25 2.25v1.5a2.25 2.25 0 01-2.25 2.25H9A10.5 10.5 0 012.25 12V6.75z" />
        </svg>
        <h3 class="font-semibold text-lg">Nomor Telepon</h3>
        <p class="mt-1">+62 877-1537-3102</p>
      </div>

      <div class="bg-blue-500 text-white w-full md:w-1/3 py-6 rounded-lg flex flex-col items-center justify-center shadow-md transition duration-500 hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8.25l8.25 6.75L19.5 8.25M3 6h18a1.5 1.5 0 011.5 1.5v9A1.5 1.5 0 0121 18H3a1.5 1.5 0 01-1.5-1.5v-9A1.5 1.5 0 013 6z" />
        </svg>
        <h3 class="font-semibold text-lg">Email</h3>
        <p class="mt-1">paudbougenville@gmail.com</p>
      </div>
    </div>

    <!-- Google Maps -->
    <div class="flex justify-center fade-in">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.212020654405!2d106.85777497498952!3d-6.233406161027171!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f33ab9a5f431%3A0x3f882c49751508cf!2sJl.%20Pinang%20Barat%2C%20Jakarta%20Timur!5e0!3m2!1sid!2sid!4v1713241946208!5m2!1sid!2sid"
        width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade" class="rounded-lg shadow-md max-w-4xl">
      </iframe>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-blue-600 text-white py-8 fade-in">
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

  <script>
    // Fade in saat elemen masuk viewport
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
  </script>
</body>
</html>
