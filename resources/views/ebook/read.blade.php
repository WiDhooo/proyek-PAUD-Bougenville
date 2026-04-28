<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Petualangan Membaca: {{ $ebook->judul }}</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(to bottom, #87CEEB 0%, #E0F7FA 100%);
            margin: 0; display: flex; flex-direction: column; height: 100vh; overflow: hidden;
            font-family: 'Fredoka', sans-serif;
        }

        .reader-header {
            background: rgba(255, 255, 255, 0.95); padding: 10px 25px; display: flex;
            justify-content: space-between; align-items: center; z-index: 1000; height: 55px;
            border-bottom: 4px solid #FFD700;
        }

        #viewport { 
            flex: 1; display: flex; justify-content: center; align-items: center; 
            position: relative; overflow: hidden; padding: 15px;
        }

        #flipbook { 
            margin: auto;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            display: none; 
        }

        .page { background-color: white; overflow: hidden; position: relative; }
        .page img { width: 100% !important; height: 100% !important; object-fit: fill !important; display: block; }

        .turn-page-shadow { display: none !important; }

        /* Tombol Audio di Pojok Gambar */
        .btn-audio-page {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: #4A90E2;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 1000;
            transition: transform 0.2s, background 0.2s;
        }
        .btn-audio-page:hover { transform: scale(1.1); background: #357ABD; }
        .btn-audio-page:active { transform: scale(0.9); }

        .btn-nav-round {
            background: #FF9900; color: white; width: 50px; height: 50px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
            border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s;
            z-index: 100;
        }

        #loading {
            position: fixed; inset: 0; background: #FFFDF5; z-index: 9999;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
    </style>
</head>
<body>

    <audio id="flip-sound" preload="auto">
        <source src="https://www.soundjay.com/misc/sounds/page-flip-01a.mp3" type="audio/mpeg">
    </audio>

    {{-- Player Audio Halaman --}}
    <audio id="page-narration" preload="auto"></audio>

    <div id="loading">
        <div class="text-center">
            <div class="animate-bounce mb-4 text-5xl text-blue-500"><i class="fas fa-book-open"></i></div>
            <p class="font-bold text-blue-600 text-xl">Menyiapkan Buku...</p>
        </div>
    </div>

    <header class="reader-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('ebook.show', $ebook->id) }}" class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-full font-bold text-xs shadow-md transition">
                <i class="fas fa-times"></i> Tutup
            </a>
            <span class="truncate font-bold text-blue-800 text-sm hidden md:block border-l-2 border-blue-200 ps-4">
                {{ $ebook->judul }}
            </span>
        </div>
        <div class="bg-blue-100 px-4 py-1 rounded-full border-2 border-blue-200 shadow-inner">
            <span id="page-label" class="text-blue-700 font-bold text-xs">1 / {{ count($pages) }}</span>
        </div>
    </header>

    <div id="viewport">
        <button class="hidden lg:flex btn-nav-round absolute left-10" onclick="$('#flipbook').turn('previous')">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div id="flipbook">
            @foreach($pages as $index => $page)
                <div class="page {{ ($index == 0 || $index == count($pages) - 1) ? 'hard' : '' }}">
                    
                    <img src="{{ asset('storage/' . $page['image']) }}" class="ebook-img" alt="P">

                    {{-- Tombol Audio Muncul jika Data Audio Ada --}}
                    @if(!empty($page['audio']))
                        <button class="btn-audio-page" 
                                onclick="playThisAudio('{{ asset('storage/' . $page['audio']) }}')">
                            <i class="fas fa-volume-up"></i>
                        </button>
                    @endif
                    
                </div>
            @endforeach
        </div>

        <button class="hidden lg:flex btn-nav-round absolute right-10" onclick="$('#flipbook').turn('next')">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <script>
        const narrationPlayer = document.getElementById("page-narration");

        // Fungsi Memutar Audio Per Halaman
        function playThisAudio(src) {
            narrationPlayer.src = src;
            narrationPlayer.play().catch(e => {
                console.log("Klik manual diperlukan untuk memutar audio.");
            });
        }

        $(document).ready(function() {
            const flipbook = $("#flipbook");
            const totalPages = {{ count($pages) }};
            const flipSound = document.getElementById("flip-sound");

            function initBook() {
                const firstImg = $('.ebook-img')[0];
                if (!firstImg || firstImg.naturalWidth === 0) {
                    setTimeout(initBook, 100);
                    return;
                }

                const winW = $(window).width();
                const winH = $(window).height() - 100;
                const isMobile = winW < 1024;
                const ratio = firstImg.naturalWidth / firstImg.naturalHeight;
                
                let bHeight, bWidth;

                if (isMobile) {
                    bWidth = winW * 0.95;
                    bHeight = bWidth / ratio;
                } else {
                    bHeight = winH * 0.9;
                    bWidth = (bHeight * ratio) * 2;
                    if (bWidth > winW * 0.95) {
                        bWidth = winW * 0.95;
                        bHeight = (bWidth / 2) / ratio;
                    }
                }

                flipbook.turn({
                    width: Math.round(bWidth),
                    height: Math.round(bHeight),
                    display: isMobile ? 'single' : 'double',
                    autoCenter: true,
                    acceleration: true,
                    gradients: true,
                    elevation: 50,
                    duration: 1000,
                    when: {
                        turning: function() {
                            flipSound.currentTime = 0;
                            flipSound.play();
                            // Berhenti audio narasi saat halaman sedang dibalik
                            narrationPlayer.pause();
                        },
                        turned: function(e, page) {
                            $("#page-label").text(page + " / " + totalPages);
                        }
                    }
                });

                $("#loading").fadeOut(300);
                flipbook.fadeIn(300);
            }

            $(window).on('load', initBook);
            if (document.readyState === 'complete') initBook();

            $(window).resize(function() {
                location.reload(); 
            });
        });
    </script>
</body>
</html>