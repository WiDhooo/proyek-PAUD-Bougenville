<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Guru') - PAUD Bougenville</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f4f7fa; }
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 260px;
            padding: 1.5rem;
            background-color: #ffffff;
            box-shadow: 0 0 1rem 0 rgba(0,0,0,.05);
            transition: width 0.3s;
        }
        .sidebar .nav-link {
            color: #555;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link:not(.active):hover {
            background-color: #e9f2ff;
            color: #0d6efd;
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            transition: margin 0.3s;
        }
        .main-content {
            margin-left: 260px;
            padding: 1.5rem;
            transition: margin-left 0.3s;
        }
        .header {
            background: #fff;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 1rem 0 rgba(0,0,0,.05);
        }

        /* Sidebar minimize */
        .sidebar.minimized {
            width: 80px;
        }
        .main-content.minimized {
            margin-left: 80px;
        }
        .sidebar.minimized .nav-link span {
            display: none;
        }
        .sidebar-text {
            transition: opacity 0.3s;
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="d-flex align-items-center mb-4 justify-content-between">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/melaju-01.png') }}" alt="Logo PAUD" style="width:40px;height:40px;border-radius:50%;margin-right:0.5rem;">
                <span class="fs-5 fw-bold sidebar-text">PAUD Bougenville</span>
            </div>
            <button id="toggleSidebar" class="btn btn-outline-secondary p-1">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <ul class="nav flex-column gap-2">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ request()->is('guru/dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                    <i class="bi bi-grid-fill"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ request()->is('guru/data-siswa*') ? 'active' : '' }}" href="{{ route('guru.data_siswa') }}">
                    <i class="bi bi-people-fill"></i> <span>Data Siswa</span>
                </a>
            </li>
<<<<<<< HEAD
=======
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ request()->is('guru/nilai-absensi*') ? 'active' : '' }}" href="{{ route('guru.nilai_absensi') }}">
                    <i class="bi bi-journal-check"></i> <span>Nilai & Absensi</span>
                </a>
            </li>
>>>>>>> ce5e812 (Update untuk GURU di bagian dashboard, data siswa, tambah model & migration)
        </ul>
    </aside>

    <main class="main-content">
        <header class="header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
        </header>
        <div>
            @yield('content')
        </div>
    </main>
<<<<<<< HEAD
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
=======

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        const sidebarText = document.querySelectorAll('.sidebar-text, .sidebar .nav-link span');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('minimized');
            mainContent.classList.toggle('minimized');

            sidebarText.forEach(el => el.classList.toggle('d-none'));

            const icon = toggleBtn.querySelector('i');
            if(sidebar.classList.contains('minimized')){
                icon.classList.replace('bi-chevron-left','bi-chevron-right');
            } else {
                icon.classList.replace('bi-chevron-right','bi-chevron-left');
            }
        });
    </script>

    @stack('scripts')
>>>>>>> ce5e812 (Update untuk GURU di bagian dashboard, data siswa, tambah model & migration)
</body>
</html>
