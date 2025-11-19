<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Guru') - PAUD Bougenville</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- **Hapus CSS Bootstrap JS, cukup CSS saja** --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fa;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: #ffffff;
            padding: 1.4rem;
            border-right: 1px solid #eaeaea;
            transition: all 0.3s ease;
        }

        .sidebar-logo {
            font-weight: 700;
            font-size: 1.3rem;
            margin-left: .5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: #555;
            font-weight: 500;
            border-radius: 8px;
            transition: .2s;
        }

        .nav-link:hover {
            background: #eef4ff;
            color: #0d6efd;
        }

        .nav-link.active {
            background: #0d6efd;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
        }

        .header {
            background: white;
            padding: 1rem 1.4rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    </style>

</head>

<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/melaju-01.png') }}" style="width:40px;height:40px;border-radius:8px;">
                <span class="sidebar-logo ms-2">PAUD Bougenville</span>
            </div>
        </div>

        <nav>
            <a class="nav-link {{ request()->is('guru/dashboard') ? 'active' : '' }}"
                href="{{ route('guru.dashboard') }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>

            <a class="nav-link {{ request()->is('guru/data-siswa*') ? 'active' : '' }}"
                href="{{ route('guru.data_siswa') }}">
                <i class="bi bi-people-fill"></i> Data Siswa
            </a>

            <a class="nav-link {{ request()->is('guru/nilai-absensi*') ? 'active' : '' }}"
                href="{{ route('guru.nilai_absensi') }}">
                <i class="bi bi-journal-check"></i> Nilai & Absensi
            </a>
        </nav>

    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        <header class="header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('title')</h4>

            {{-- USER DROPDOWN --}}
            <div class="dropdown">
                <a href="#" class="dropdown-toggle d-flex align-items-center text-dark text-decoration-none"
                    data-bs-toggle="dropdown">

                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <span class="fw-bold">{{ Auth::user()->name }}</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <div>
            @yield('content')
        </div>

    </main>

    {{-- ❗ HAPUS yang dulu — cukup Vite yang load Bootstrap JS --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}

</body>

</html>
