<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Admin') - PAUD Bougenville</title>

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
        /* ====== DESIGN TOKENS — PAUD Bougenville Admin ====== */
        :root {
            /* Primary: Blue */
            --paud-teal: #2563EB;
            --paud-teal-light: #EFF6FF;
            --paud-teal-hover: #1D4ED8;
            /* Accent: Orange */
            --paud-amber: #FF9900;
            --paud-amber-light: #FFF7E6;
            /* Success: Green */
            --paud-green: #16A34A;
            --paud-green-light: #F0FDF4;
            /* Danger */
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
            overflow-y: auto;
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

        /* ====== NAVIGATION ====== */
        .sidebar-section-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--paud-muted);
            padding: 8px 14px 4px;
            margin-top: 0.5rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--paud-muted);
            font-weight: 500;
            font-size: 0.88rem;
            border-radius: var(--paud-radius-sm);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
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

        /* Submenu */
        .submenu-wrapper {
            padding-left: 12px;
            border-left: 2px solid var(--paud-border);
            margin-left: 22px;
            margin-top: 2px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .submenu-wrapper .nav-link {
            font-size: 0.85rem;
            padding: 8px 12px;
        }

        /* Chevron */
        .chevron { transition: transform 0.25s ease; }
        .chevron.open { transform: rotate(180deg); }

        /* ====== SIDEBAR FOOTER ====== */
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

        /* ====== HEADER ====== */
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
            font-size: 1.05rem;
            color: var(--paud-text);
            margin: 0;
        }

        /* ====== REUSABLE COMPONENTS ====== */
        .paud-card {
            background: var(--paud-card);
            border: none;
            border-radius: var(--paud-radius);
            box-shadow: var(--paud-shadow);
            transition: box-shadow 0.2s ease;
        }

        .paud-card:hover { box-shadow: var(--paud-shadow-hover); }

        .paud-badge {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }
        .bg-paud-teal-light { background-color: var(--paud-teal-light) !important; }
        .text-paud-teal { color: var(--paud-teal) !important; }

        .paud-btn-primary {
            background: var(--paud-teal);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: var(--paud-radius-sm);
            padding: 8px 18px;
            transition: all 0.2s ease;
            font-size: 0.88rem;
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
            font-size: 0.88rem;
        }
        .paud-btn-outline:hover {
            background: var(--paud-teal-light);
            color: var(--paud-teal);
        }

        .paud-btn-danger {
            background: var(--paud-coral);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: var(--paud-radius-sm);
            padding: 7px 18px;
            transition: all 0.2s ease;
            font-size: 0.88rem;
        }
        .paud-btn-danger:hover {
            background: #DC2626;
            color: #fff;
        }

        .paud-btn-outline-danger {
            background: transparent;
            border: 1.5px solid var(--paud-coral);
            color: var(--paud-coral);
            font-weight: 600;
            border-radius: var(--paud-radius-sm);
            padding: 7px 18px;
            transition: all 0.2s ease;
            font-size: 0.88rem;
        }
        .paud-btn-outline-danger:hover {
            background: var(--paud-coral-light);
            color: var(--paud-coral);
        }

        .paud-thead {
            background: var(--paud-teal);
            color: #fff;
        }
        .paud-thead th {
            font-size: 0.82rem;
            font-weight: 600;
            border: none;
            padding: 12px 14px;
        }

        .paud-table-row { transition: background 0.15s ease; }
        .paud-table-row:hover { background: var(--paud-teal-light); }

        .icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Modal overrides */
        .modal-content {
            border: none;
            border-radius: var(--paud-radius);
            box-shadow: var(--paud-shadow-hover);
        }
        .modal-header {
            border-bottom: 1px solid var(--paud-border);
            padding: 1rem 1.4rem;
        }
        .modal-footer {
            border-top: 1px solid var(--paud-border);
            padding: 0.85rem 1.4rem;
        }
        .modal-title { font-weight: 600; font-size: 1rem; color: var(--paud-text); }

        .form-control, .form-select {
            border-color: var(--paud-border);
            border-radius: var(--paud-radius-sm);
            font-size: 0.88rem;
            font-family: 'Poppins', sans-serif;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--paud-teal);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--paud-text);
            margin-bottom: 6px;
        }

        /* Utility */
        .text-paud-teal  { color: var(--paud-teal) !important; }
        .text-paud-amber { color: var(--paud-amber) !important; }
        .text-paud-coral { color: var(--paud-coral) !important; }
        .text-paud-green { color: var(--paud-green) !important; }
        .bg-paud-teal        { background: var(--paud-teal) !important; }
        .bg-paud-teal-light  { background: var(--paud-teal-light) !important; }
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
                <div class="sidebar-brand-sub">Panel Admin</div>
            </div>

        </div>

        <nav class="sidebar-nav" x-data="{
                open: (() => {
                    @if(request()->is('admin/guru*') || request()->is('admin/kelas*') || request()->is('admin/siswa*'))
                        return true;
                    @endif
                    const saved = localStorage.getItem('paud_sidebar_data_sekolah');
                    return saved !== null ? saved === 'true' : false;
                })(),
                toggle() {
                    this.open = !this.open;
                    localStorage.setItem('paud_sidebar_data_sekolah', this.open);
                }
            }">


            {{-- Dashboard --}}
            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>


            {{-- Data Sekolah (collapsible, state persisted) --}}
            <a class="nav-link justify-content-between
                       {{ request()->is('admin/guru*') || request()->is('admin/kelas*') || request()->is('admin/siswa*') ? 'active' : '' }}"
               href="#" @click.prevent="toggle()">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-building"></i> Data Sekolah
                </span>
                <i class="bi bi-chevron-down chevron" :class="{ 'open': open }"></i>
            </a>

            <div class="submenu-wrapper" x-show="open" x-transition>
                <a href="{{ route('admin.guru.index') }}"
                   class="nav-link {{ request()->is('admin/guru*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i> Guru
                </a>
                <a href="{{ route('admin.siswa.index') }}"
                   class="nav-link {{ request()->is('admin/siswa*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Siswa
                </a>
                <a href="{{ route('admin.kelas.index') }}"
                   class="nav-link {{ request()->is('admin/kelas*') ? 'active' : '' }}">
                    <i class="bi bi-door-open-fill"></i> Kelas
                </a>
            </div>

            {{-- E-Book --}}
            <a class="nav-link {{ request()->is('admin/ebook*') ? 'active' : '' }}" 
            href="{{ route('admin.ebook.index') }}">
                <i class="bi bi-journal-text"></i> Manajemen E-Book
            </a>

            {{-- Keuangan --}}
            <a class="nav-link {{ request()->is('admin/keuangan*') ? 'active' : '' }}" 
            href="{{ route('admin.keuangan.index') }}">
                <i class="bi bi-wallet2"></i> Keuangan
            </a>


            {{-- Profil Sekolah --}}
            <a class="nav-link {{ request()->is(['admin/profil*', 'admin/galeri*']) ? 'active' : '' }}"
               href="{{ route('admin.profil.index') }}">
                <i class="bi bi-building-gear"></i> Profil Sekolah
            </a>

        </nav>

        {{-- Sidebar Footer --}}
        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2 px-2">
                <div class="icon-circle bg-paud-teal-light text-paud-teal" style="width:36px;height:36px;">
                    <i class="bi bi-shield-check" style="font-size:0.9rem;"></i>
                </div>
                <div style="min-width:0;">
                    <div class="fw-semibold text-truncate" style="font-size:0.82rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div style="font-size:0.72rem; color: var(--paud-muted);">Administrator</div>
                </div>
            </div>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        <header class="header">
            <h4>@yield('title', 'Dashboard')</h4>

            {{-- USER DROPDOWN --}}
            <div class="dropdown">
                <a href="#" class="dropdown-toggle d-flex align-items-center text-decoration-none"
                    data-bs-toggle="dropdown" style="color: var(--paud-text);">
                    <i class="bi bi-person-circle fs-5 me-2" style="color: var(--paud-teal);"></i>
                    <span class="fw-semibold" style="font-size:0.9rem;">{{ Auth::user()->name ?? 'Admin' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2"
                    style="border-radius: var(--paud-radius-sm); font-size:0.88rem;">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger" style="font-family:'Poppins',sans-serif;">
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