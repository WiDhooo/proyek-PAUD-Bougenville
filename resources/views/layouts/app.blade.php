<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Admin') - PAUD Bougenville</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fa;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: #ffffff;
            padding: 1.4rem;
            border-right: 1px solid #eaeaea;
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
            font-weight: 600;
        }

        /* Submenu */
        .submenu {
            padding-left: 35px;
            display: none;
        }

        .submenu.show {
            display: block !important;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
        }

        /* Header */
        .header {
            background: white;
            padding: 1rem 1.4rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .rotate {
            transform: rotate(180deg);
            transition: .3s;
        }
    </style>

</head>

<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        {{-- Logo --}}
        <div class="d-flex align-items-center mb-4">
            <img src="{{ asset('images/melaju-01.png') }}" style="width:40px;height:40px;border-radius:8px;">
            <span class="sidebar-logo ms-2">PAUD Bougenville</span>
        </div>
        <div x-data="{ open: {{ request()->is('admin/guru*') || request()->is('admin/kelas*') || request()->is('admin/murid*') ? 'true' : 'false' }} }">
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-fill"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center justify-content-between" href="#" @click.prevent="open = !open">
                        <span>
                            <i class="bi bi-bank"></i>
                            Data Sekolah
                        </span>
                        <i class="bi bi-chevron-down" :class="{'transform: rotate-180deg;': open}"></i>
                    </a>
                    <ul class="nav flex-column mt-2 ms-3 gap-2 submenu" x-show="open" x-transition>
                        <li class="nav-item">
                            <a href="{{ route('admin.guru.index') }}" class="nav-link d-flex align-items-center {{ request()->is('admin/guru*') ? 'active' : '' }}">
                                <i class="bi bi-person-badge-fill"></i> Guru
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.murid.index') }}" class="nav-link d-flex align-items-center {{ request()->is('admin/murid*') ? 'active' : '' }}">
                                <i class="bi bi-person-fill"></i> Murid
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="{{ route('admin.kelas.index') }}" class="nav-link d-flex align-items-center {{ request()->is('admin/kelas*') ? 'active' : '' }}">
                                <i class="bi bi-house-door-fill"></i> Kelas
                            </a>
                        </li>
                    </ul>
                </li>
                 <li class="nav-item">
                 <a class="nav-link d-flex align-items-center {{ request()->is('admin/profil*') || request()->is('admin/galeri*')  ? 'active' : '' }}" href="{{ route('admin.profil.index') }}">
                    <i class="bi bi-person-vcard"></i>
                    Profil Sekolah
                </a>
                </li>
            </ul>
        </div>
    </aside>
    <main class="main-content">
    <header class="header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
        <nav>
            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>

            {{-- DROPDOWN DATA SEKOLAH --}}
            <a href="#" class="nav-link d-flex justify-content-between align-items-center" id="toggleSekolah">
                <span><i class="bi bi-bank"></i> Data Sekolah</span>
                <i class="bi bi-chevron-down" id="iconSekolah"></i>
            </a>

            <div class="submenu {{ request()->is('admin/guru*') || request()->is('admin/murid*') || request()->is('admin/kelas*') ? 'show' : '' }}"
                id="submenuSekolah">

                <a href="{{ route('admin.guru.index') }}"
                    class="nav-link {{ request()->is('admin/guru*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i> Guru
                </a>

                <a href="{{ route('admin.murid.index') }}"
                    class="nav-link {{ request()->is('admin/murid*') ? 'active' : '' }}">
                    <i class="bi bi-person-fill"></i> Murid
                </a>

                <a href="{{ route('admin.kelas.index') }}"
                    class="nav-link {{ request()->is('admin/kelas*') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Kelas
                </a>
            </div>

            <a class="nav-link {{ request()->is('admin/profil-sekolah*') ? 'active' : '' }}"
                href="{{ route('admin.profil.index') }}">
                <i class="bi bi-person-vcard"></i> Profil Sekolah
            </a>

        </nav>

    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        {{-- HEADER --}}
        <header class="header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('title')</h4>

            {{-- USER DROPDOWN --}}
            <div class="dropdown">
                <a href="#" class="dropdown-toggle d-flex align-items-center text-dark text-decoration-none"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <span class="fw-bold">{{ Auth::user()->name }}</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
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

    {{-- Dropdown Data Sekolah --}}
    <script>
        document.getElementById('toggleSekolah').addEventListener('click', function(e) {
            e.preventDefault();

            let submenu = document.getElementById('submenuSekolah');
            let icon = document.getElementById('iconSekolah');

            submenu.classList.toggle('show');
            icon.classList.toggle('rotate');
        });
    </script>

</body>

</html>
