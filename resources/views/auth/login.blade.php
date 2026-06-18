<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem - Apotek Pro System</title>

    <!-- Google Fonts: Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-container">

        <!-- Sisi Kiri - Informasi & Ornamen (Mockup Match) -->
        <div class="login-info-side">
            <!-- Background Ornaments -->
            <!-- Circles -->
            <div class="ornament ornament-circle" style="width: 140px; height: 140px; top: -40px; left: 10%; animation: float 8s ease-in-out infinite;"></div>
            <div class="ornament ornament-circle" style="width: 60px; height: 60px; bottom: 80px; left: 20%; animation: float 6s ease-in-out infinite 1s;"></div>
            <div class="ornament ornament-circle" style="width: 100px; height: 100px; top: 40%; right: 5%; animation: float 9s ease-in-out infinite 0.5s;"></div>
            <div class="ornament ornament-circle-filled" style="width: 16px; height: 16px; top: 120px; right: 25%; opacity: 0.15; animation: float 5s ease-in-out infinite 2s;"></div>
            <div class="ornament ornament-circle-filled" style="width: 12px; height: 12px; bottom: 180px; right: 40%; opacity: 0.12; animation: float 4s ease-in-out infinite 1.5s;"></div>
            <div class="ornament ornament-circle-filled" style="width: 8px; height: 8px; top: 45%; left: 8%; opacity: 0.18;"></div>

            <!-- Squares & Triangles -->
            <div class="ornament" style="width: 40px; height: 40px; bottom: 100px; left: 35%; transform: rotate(15deg); animation: float 7s ease-in-out infinite 0.5s;"></div>
            <div class="ornament" style="width: 30px; height: 30px; top: 50px; right: 15%; transform: rotate(45deg); animation: float 8s ease-in-out infinite 1.2s;"></div>

            <!-- Custom Triangles -->
            <div class="ornament" style="width: 0; height: 0; border-left: 20px solid transparent; border-right: 20px solid transparent; border-bottom: 35px solid rgba(255,255,255,0.15); border-top: none; background: none; top: 220px; left: 42%; transform: rotate(-10deg);"></div>
            <div class="ornament" style="width: 0; height: 0; border-left: 12px solid transparent; border-right: 12px solid transparent; border-bottom: 22px solid rgba(255,255,255,0.15); border-top: none; background: none; bottom: 240px; left: 12%; transform: rotate(25deg);"></div>

            <!-- Diagonal arrow/double caret icons -->
            <svg class="ornament" style="width: 40px; height: 40px; top: 120px; left: 35%; stroke: #ffffff; stroke-width: 2; fill: none; opacity: 0.15;" viewBox="0 0 24 24">
                <path d="M7 17L17 7M17 7H9M17 7V15" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <svg class="ornament" style="width: 40px; height: 40px; bottom: 180px; left: 10%; stroke: #ffffff; stroke-width: 2; fill: none; opacity: 0.15;" viewBox="0 0 24 24">
                <path d="M7 17L17 7M17 7H9M17 7V15" stroke-linecap="round" stroke-linejoin="round" transform="rotate(180 12 12)" />
            </svg>
            <svg class="ornament" style="width: 48px; height: 48px; bottom: 80px; right: 15%; stroke: #ffffff; stroke-width: 2; fill: none; opacity: 0.15;" viewBox="0 0 24 24">
                <path d="M13 5l7 7-7 7M6 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            <!-- Brand Section -->
            <div class="brand-logo-wrapper">
                <div class="brand-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <h1 class="brand-title">Apotek Pro<br>System</h1>
                <p class="brand-desc">Kelola inventaris dan layanan farmasi dengan presisi tinggi dan antarmuka yang nyaman.</p>
            </div>

            <!-- Footer label -->
            <div style="font-size: 0.85rem; opacity: 0.7; font-weight: 500; z-index: 10;">
                Aplikasi Manajemen Internal v1.0
            </div>
        </div>

        <!-- Sisi Kanan - Form Login -->
        <div class="login-form-side">
            <div class="form-header">
                <h2 class="form-title">Masuk ke Sistem</h2>
                <p class="form-desc">Gunakan kredensial yang terdaftar untuk akses dashboard.</p>
            </div>

            <!-- Error message global -->
            @if ($errors->has('username'))
            <div class="alert-danger-custom">
                {{ $errors->first('username') }}
            </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input @error('username') border-red-500 @enderror"
                        placeholder="Masukkan username"
                        value="{{ old('username') }}"
                        required
                        autofocus>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div style="position: relative;">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') border-red-500 @enderror"
                            placeholder="Password"
                            style="padding-right: 44px;"
                            required>
                        <button type="button" id="togglePassword" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-secondary); display: flex; align-items: center; padding: 4px; outline: none;">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat Saya
                    </label>
                    <a href="#" class="forgot-link" onclick="alert('Silakan hubungi administrator apotek untuk mereset kata sandi Anda.')">Lupa Sandi?</a>
                </div>

                <button type="submit" class="btn-submit">
                    Masuk Sekarang
                </button>
            </form>

            <div class="form-footer">
                <span>© Apotek Pro System</span>
                <span class="footer-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 10.5h-5.5V5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v5.5H5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5h5.5V19c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5v-5.5H19c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5z" />
                    </svg>
                </span>
            </div>
        </div>

    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                    <line x1="1" y1="1" x2="23" y2="23" />
                `;
            }
        });
    </script>
</body>

</html>