<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Guru') - PAUD Bougenville</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #f4f7fa; }
        .sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 260px; padding: 1.5rem; background-color: #ffffff; box-shadow: 0 0 1rem 0 rgba(0,0,0,.05); }
        .sidebar .nav-link { color: #555; font-weight: 500; padding: 0.75rem 1rem; border-radius: 0.5rem; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; } /* Warna hijau untuk guru */
        .sidebar .nav-link:not(.active):hover { background-color: #f8f9fa; }
        .sidebar .nav-link i { margin-right: 0.75rem; }
        .main-content { margin-left: 260px; padding: 1.5rem; }
        .header { background: #fff; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; box-shadow: 0 0 1rem 0 rgba(0,0,0,.05); }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="d-flex align-items-center mb-4"><span class="fs-5 fw-bold">PAUD Bougenville</span></div>
        <ul class="nav flex-column gap-2">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ request()->is('guru/dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                    <i class="bi bi-grid-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ request()->is('guru/data-siswa*') ? 'active' : '' }}" href="{{ route('guru.data_siswa') }}">
                    <i class="bi bi-people-fill"></i> Data Siswa
                </a>
            </li>
        </ul>
    </aside>
    <main class="main-content">
        <header class="header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
            <div><span>Nama Guru</span></div>
        </header>
        <div>
            @yield('content')
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (Session::has('success'))
                toastr.success("{{ Session::get('success') }}");
            @endif

            @if (Session::has('error'))
                toastr.error("{{ Session::get('error') }}");
            @endif

            @if (Session::has('info'))
                toastr.info("{{ Session::get('info') }}");
            @endif

            @if (Session::has('warning'))
                toastr.warning("{{ Session::get('warning') }}");
            @endif
        });
    </script>
</body>
</html>