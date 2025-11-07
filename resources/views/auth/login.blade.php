<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PAUD Bougenville</title>

    {{-- Memuat CSS & JS dari Vite (Bootstrap, Alpine, dll) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Menggunakan gradien yang mirip dengan referensi */
        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #dee8ff 100%);
        }
        .form-control-lg {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-4">

    <div class="w-100" style="max-width: 450px;">
        
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
            
            <div class="card-header bg-primary bg-gradient p-5 text-center text-white border-0">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-mortarboard-fill fs-2 text-primary"></i>
                </div>
                <h1 class="h2 fw-bold text-white mb-1">PAUD Bougenville</h1>
            </div>

            <div class="card-body p-4 p-md-5" x-data="{ showPassword: false }">

                <form action="{{ route('login.attempt') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0">
                                <i class="bi bi-person text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-0" id="email" name="email" placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input :type="showPassword ? 'text' : 'password'" class="form-control border-0" id="password" name="password" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary border-0" type="button" @click="showPassword = !showPassword">
                                <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label small" for="rememberMe">
                                Ingat saya
                            </label>
                        </div>
                        <a href="#" class="small text-decoration-none">Lupa password?</a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold bg-gradient shadow">
                            Masuk
                        </button>
                    </div>

                </form>
            </div>

            <div class="card-footer text-center py-3 bg-light border-0">
                <p class="text-muted small mb-0">
                    Kembali ke 
                    <a href="{{ url('/') }}" class="fw-bold text-decoration-none">
                        Halaman Utama
                    </a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>