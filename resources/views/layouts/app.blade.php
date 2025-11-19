<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - PAUD Bougenville</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f4f7fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            padding: 1.5rem;
            background-color: #ffffff;
            box-shadow: 0 0 1rem 0 rgba(0,0,0,.05);
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #555;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link:not(.active):hover {
            background-color: #f8f9fa;
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
        }
        .sidebar .submenu .nav-link {
            padding-left: 2.75rem;
            font-size: 0.9rem;
        }
        .main-content {
            margin-left: 260px;
            padding: 1.5rem;
        }
        .header {
            background: #fff;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 1rem 0 rgba(0,0,0,.05);
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="d-flex align-items-center mb-4">
            <span class="fs-5 fw-bold">PAUD Bougenville</span>
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

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <span class="fw-bold">Nama User</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    {{-- Pastikan ID form ini unik --}}
                    <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0" id="guru-logout-form">
                        @csrf
                        <a href="#" 
                        onclick="event.preventDefault(); document.getElementById('guru-logout-form').submit();" 
                        class="d-flex align-items-center text-danger p-2" 
                        style="text-decoration: none;">
                            <i class="bi bi-box-arrow-left me-2" style="font-size: 1.1rem;"></i>
                            Keluar
                        </a>
                    </form>
                </li>
            </ul>
        </div>

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