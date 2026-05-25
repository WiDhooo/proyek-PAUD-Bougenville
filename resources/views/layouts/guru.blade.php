<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Guru') - PAUD Bougenville</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Fonts: Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ====== DESIGN TOKENS — PAUD Bougenville ====== */
        :root {
            /* Primary: Blue (sesuai website) */
            --paud-teal: #2563EB;
            --paud-teal-light: #EFF6FF;
            --paud-teal-hover: #1D4ED8;
            /* Accent: Orange */
            --paud-amber: #FF9900;
            --paud-amber-light: #FFF7E6;
            /* Success: Green */
            --paud-green: #16A34A;
            --paud-green-light: #F0FDF4;
            /* Danger/Coral */
            --paud-coral: #EF4444;
            --paud-coral-light: #FEF2F2;
            /* Backgrounds */
            --paud-bg: #FFFDF5;
            --paud-card: #FFFFFF;
            --paud-sidebar: #FFFFFF;
            /* Text */
            --paud-text: #1E293B;
            --paud-muted: #64748B;
            --paud-border: #E2E8F0;
            /* Shadows */
            --paud-shadow: 0 1px 8px rgba(37, 99, 235, 0.06);
            --paud-shadow-hover: 0 4px 20px rgba(37, 99, 235, 0.12);
            /* Radius */
            --paud-radius: 12px;
            --paud-radius-sm: 8px;
        }

        /* ====== GLOBAL ====== */
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--paud-bg);
            color: var(--paud-text);
            -webkit-font-smoothing: antialiased;
        }

        /* ====== SIDEBAR ====== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--paud-sidebar);
            padding: 1.5rem 1.2rem;
            border-right: 1px solid var(--paud-border);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 1.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--paud-border);
        }

        .sidebar-brand img {
            width: 42px;
            height: 42px;
            border-radius: 10px;
        }

        .sidebar-brand-text {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--paud-teal);
            line-height: 1.2;
        }

        .sidebar-brand-sub {
            font-size: 0.72rem;
            color: var(--paud-muted);
            font-weight: 500;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: var(--paud-muted);
            font-weight: 500;
            font-size: 0.93rem;
            border-radius: var(--paud-radius-sm);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--paud-teal-light);
            color: var(--paud-teal);
        }

        .sidebar-nav .nav-link.active {
            background: var(--paud-teal);
            color: #fff;
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid var(--paud-border);
        }

        /* ====== MAIN CONTENT ====== */
        .main-content {
            margin-left: 260px;
            padding: 1.5rem 2rem;
            min-height: 100vh;
        }

        .header {
            background: var(--paud-card);
            padding: 1rem 1.5rem;
            border-radius: var(--paud-radius);
            margin-bottom: 1.5rem;
            box-shadow: var(--paud-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h4 {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--paud-text);
            margin: 0;
        }

        /* ====== REUSABLE CLASSES ====== */
        .paud-card {
            background: var(--paud-card);
            border: none;
            border-radius: var(--paud-radius);
            box-shadow: var(--paud-shadow);
            transition: box-shadow 0.2s ease;
        }

        .paud-card:hover {
            box-shadow: var(--paud-shadow-hover);
        }

        .paud-badge {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .paud-btn-primary {
            background: var(--paud-teal);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: var(--paud-radius-sm);
            padding: 8px 18px;
            transition: all 0.2s ease;
        }

        .paud-btn-primary:hover {
            background: var(--paud-teal-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .paud-btn-outline {
            background: transparent;
            border: 1.5px solid var(--paud-teal);
            color: var(--paud-teal);
            font-weight: 600;
            border-radius: var(--paud-radius-sm);
            padding: 7px 18px;
            transition: all 0.2s ease;
        }

        .paud-btn-outline:hover {
            background: var(--paud-teal-light);
            color: var(--paud-teal);
        }

        .paud-thead {
            background: var(--paud-teal);
            color: #fff;
        }

        .paud-thead th {
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            padding: 14px 16px;
        }

        .paud-table-row {
            transition: background 0.15s ease;
        }

        .paud-table-row:hover {
            background: var(--paud-teal-light);
        }

        .icon-circle {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .text-paud-teal { color: var(--paud-teal) !important; }
        .text-paud-amber { color: var(--paud-amber) !important; }
        .text-paud-coral { color: var(--paud-coral) !important; }
        .bg-paud-teal { background: var(--paud-teal) !important; }
        .bg-paud-teal-light { background: var(--paud-teal-light) !important; }
        .bg-paud-amber-light { background: var(--paud-amber-light) !important; }
        .bg-paud-coral-light { background: var(--paud-coral-light) !important; }
        .bg-paud-green-light { background: var(--paud-green-light) !important; }
    </style>

</head>

<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar">

    <div class="sidebar-brand">
    
        {{-- LOGO --}}
        <img src="{{ asset('images/logo-paud.png') }}" 
            alt="Logo PAUD Bougenville"
            class="sidebar-logo">

            {{-- TEXT --}}
            <div> 
                <div class="sidebar-brand-text">PAUD Bougenville</div>
                <div class="sidebar-brand-sub">Panel Guru</div>
            </div>

        </div>

        <nav class="sidebar-nav">
            <a class="nav-link {{ request()->is('guru/dashboard') ? 'active' : '' }}"
                href="{{ route('guru.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <a class="nav-link {{ request()->is('guru/data-siswa*') ? 'active' : '' }}"
                href="{{ route('guru.data_siswa') }}">
                <i class="bi bi-people-fill"></i> Data Siswa
            </a>

            <a class="nav-link {{ request()->is('guru/absensi*') ? 'active' : '' }}"
                href="{{ route('guru.absensi.index') }}">
                <i class="bi bi-clipboard-check"></i> Absensi
            </a>

            <a class="nav-link {{ request()->is('guru/rapor*') ? 'active' : '' }}"
                href="{{ route('guru.rapor.pilih_kelas') }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Analisis dan Rekomendasi Minat Bakat
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2 px-2">
                <div class="icon-circle bg-paud-teal-light text-paud-teal" style="width:36px;height:36px;">
                    <i class="bi bi-person-fill" style="font-size:0.9rem;"></i>
                </div>
                <div style="min-width:0;">
                    <div class="fw-semibold text-truncate" style="font-size:0.85rem;">{{ Auth::user()->name }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Guru</div>
                </div>
            </div>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        <header class="header">
            <h4>@yield('title')</h4>

            {{-- USER DROPDOWN --}}
            <div class="dropdown">
                <a href="#" class="dropdown-toggle d-flex align-items-center text-decoration-none"
                    data-bs-toggle="dropdown" style="color: var(--paud-text);">
                    <i class="bi bi-person-circle fs-5 me-2" style="color: var(--paud-teal);"></i>
                    <span class="fw-semibold" style="font-size:0.9rem;">{{ Auth::user()->name }}</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: var(--paud-radius-sm);">
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

</body>

</html>
