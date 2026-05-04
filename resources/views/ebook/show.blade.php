<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ebook->judul }} - PAUD Bougenville</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-[#FFFDF5] text-gray-800">

    {{-- NAVBAR --}}
    <nav class="fixed top-0 left-0 w-full bg-white/90 backdrop-blur-md shadow-xl z-50">
        <div class="container mx-auto px-6 md:px-24 flex justify-between items-center py-4">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600">PAUD Bougenville</a>
        </div>
    </nav>

    {{-- CONTENT --}}
    <section class="pt-32 pb-20 px-6 md:px-24">

        {{-- BACK BUTTON --}}
        <div class="max-w-6xl mx-auto mb-6">
            <a href="{{ route('beranda') }}" 
            class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-gray-700 font-medium transition shadow-sm w-fit">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>

        <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="p-8 md:p-16">
                <div class="flex flex-col md:flex-row gap-12 items-start">

                    {{-- COVER --}}
                    <div class="w-full md:w-1/3 flex flex-col items-center">
                        <div class="w-full shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
                            @php
                                $pages = $ebook->file_path;
                                if (is_string($pages)) {
                                    $pages = json_decode($pages, true);
                                }

                                if ($ebook->thumbnail) {
                                    $cover = asset('storage/' . $ebook->thumbnail);
                                } elseif (is_array($pages) && count($pages) > 0) {
                                    $cover = asset('storage/' . $pages[0]['image']);
                                } else {
                                    $cover = asset('images/default-ebook.png');
                                }
                            @endphp

                            <img src="{{ $cover }}" 
                                 class="w-full aspect-[3/4] object-cover"
                                 onerror="this.src='{{ asset('images/default-ebook.png') }}'">
                        </div>
                    </div>

                    {{-- DETAIL --}}
                    <div class="w-full md:w-2/3">
                        <h1 class="text-3xl md:text-5xl font-bold mb-2">{{ $ebook->judul }}</h1>

                        <div class="mb-6 text-sm text-gray-500">
                            {{ is_array($pages) ? count($pages) : 0 }} Halaman
                        </div>

                        <div class="mb-10">
                            <h3 class="font-bold text-xl mb-4">Sinopsis :</h3>
                            <p class="text-gray-600 text-justify">
                                {{ $ebook->deskripsi ?? 'Belum ada deskripsi.' }}
                            </p>
                        </div>

                        <a href="{{ route('ebook.read', $ebook->id) }}" 
                           class="bg-red-500 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold inline-flex items-center gap-2">
                            <i class="fas fa-book-open"></i>
                            Baca Sekarang
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- SCRIPT --}}
    <script>
        function goBack() {
            if (document.referrer) {
                window.history.back();
            } else {
                window.location.href = "{{ url('/') }}";
            }
        }
    </script>

</body>
</html>