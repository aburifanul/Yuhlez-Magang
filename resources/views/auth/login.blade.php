<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Masuk - Yuhlez Magang</title>

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    @vite([
        'resources/css/auth.css',
        'resources/js/auth.js'
    ])

</head>

<body>

<div class="auth-page">

    {{-- =====================================================
        LEFT SIDE
    ====================================================== --}}

    <div class="auth-showcase">

        <div class="showcase-overlay"></div>

        <div class="showcase-content">

            {{-- LOGO --}}
            <a href="{{ url('/') }}" class="auth-logo">

                <div class="auth-logo-icon">
                    Y
                </div>

                <div class="auth-logo-text">

                    <strong>YUHLEZ</strong>

                    <span>MAGANG</span>

                </div>

            </a>


            {{-- MAIN TEXT --}}
            <div class="showcase-main">

                <span class="showcase-label">
                    PLATFORM MAGANG
                </span>

                <h1>
                    Mulai Langkahmu
                    Menuju Dunia
                    <span>Profesional.</span>
                </h1>

                <p>
                    Temukan berbagai program magang dan perusahaan
                    yang sesuai dengan minatmu. Bangun pengalaman,
                    kembangkan kemampuan, dan persiapkan masa depanmu.
                </p>

            </div>


            {{-- STATISTIC --}}
            <div class="showcase-stats">

                <div class="showcase-stat">

                    <strong>100+</strong>

                    <span>
                        Program Magang
                    </span>

                </div>


                <div class="showcase-stat">

                    <strong>50+</strong>

                    <span>
                        Perusahaan
                    </span>

                </div>


                <div class="showcase-stat">

                    <strong>2K+</strong>

                    <span>
                        Mahasiswa
                    </span>

                </div>

            </div>

        </div>


        {{-- DECORATION --}}
        <div class="showcase-decoration decoration-one"></div>
        <div class="showcase-decoration decoration-two"></div>
        <div class="showcase-decoration decoration-three"></div>

    </div>


    {{-- =====================================================
        RIGHT SIDE
    ====================================================== --}}

    <div class="auth-content">

        <div class="auth-card">


            {{-- MOBILE LOGO --}}
            <a
                href="{{ url('/') }}"
                class="auth-logo mobile-logo"
            >

                <div class="auth-logo-icon">
                    Y
                </div>

                <div class="auth-logo-text">

                    <strong>YUHLEZ</strong>

                    <span>MAGANG</span>

                </div>

            </a>


            {{-- HEADING --}}
            <div class="auth-heading">

                <span class="auth-heading-label">
                    SELAMAT DATANG KEMBALI
                </span>

                <h2>
                    Masuk ke akunmu
                </h2>

                <p>
                    Masukkan email dan password untuk melanjutkan.
                </p>

            </div>


            {{-- =================================================
                SESSION STATUS
            ================================================== --}}

            @if (session('status'))

                <div class="auth-alert success">

                    <i class="bi bi-check-circle"></i>

                    <span>
                        {{ session('status') }}
                    </span>

                </div>

            @endif


            {{-- =================================================
                GENERAL ERROR
            ================================================== --}}

            @if ($errors->any())

                <div class="auth-alert error">

                    <i class="bi bi-exclamation-circle"></i>

                    <div>

                        @foreach ($errors->all() as $error)

                            <span>
                                {{ $error }}
                            </span>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- =================================================
                LOGIN FORM
            ================================================== --}}

            <form
                action="{{ route('login.process') }}"
                method="POST"
                class="auth-form"
                id="loginForm"
            >

                @csrf


                {{-- EMAIL --}}
                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <div class="input-wrapper">

                        <i class="bi bi-envelope"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            autofocus
                            required
                        >

                    </div>

                    @error('email')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- PASSWORD --}}
                <div class="form-group">

                    <div class="password-label">

                        <label for="password">
                            Password
                        </label>

                        <a href="#">
                            Lupa password?
                        </a>

                    </div>


                    <div class="input-wrapper">

                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="Tampilkan password"
                        >

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    @error('password')

                        <small class="field-error">
                            {{ $message }}
                        </small>

                    @enderror

                </div>


                {{-- REMEMBER ME --}}
                <div class="form-options">

                    <label class="remember-option">

                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <span class="custom-checkbox"></span>

                        <span>
                            Ingat saya
                        </span>

                    </label>

                </div>


                {{-- LOGIN BUTTON --}}
                <button
                    type="submit"
                    class="login-button"
                    id="loginButton"
                >

                    <span class="button-text">
                        Masuk
                    </span>

                    <span class="button-loading">

                        <span class="loading-spinner"></span>

                        Memproses...

                    </span>

                    <i class="bi bi-arrow-right"></i>

                </button>

            </form>


            {{-- =================================================
                GOOGLE LOGIN
            ================================================== --}}

            <div class="auth-divider">

                <span></span>

                <p>
                    atau
                </p>

                <span></span>

            </div>


            <a
                href="{{ route('google.redirect') }}"
                class="google-button"
            >

                <span class="google-icon">

                    <svg
                        viewBox="0 0 24 24"
                        width="20"
                        height="20"
                    >

                        <path
                            fill="#4285F4"
                            d="M21.35 12.27c0-.79-.07-1.55-.2-2.27H12v4.3h5.22a4.46 4.46 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.93-4.18 2.93-7.4z"
                        />

                        <path
                            fill="#34A853"
                            d="M12 21.6c2.63 0 4.84-.87 6.45-2.36l-3.14-2.44c-.87.58-1.98.93-3.31.93-2.54 0-4.7-1.72-5.47-4.04H3.28v2.52A9.74 9.74 0 0 0 12 21.6z"
                        />

                        <path
                            fill="#FBBC05"
                            d="M6.53 13.69a5.86 5.86 0 0 1 0-3.38V7.79H3.28a9.6 9.6 0 0 0 0 8.42l3.25-2.52 3.25 2.52z"
                        />

                        <path
                            fill="#EA4335"
                            d="M12 6.27c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.34 14.63 2.4 12 2.4a9.74 9.74 0 0 0-8.72 5.39l3.25 2.52C7.3 7.99 9.46 6.27 12 6.27z"
                        />

                    </svg>

                </span>

                <span>
                    Lanjutkan dengan Google
                </span>

            </a>


            {{-- REGISTER --}}
            <div class="register-text">

                Belum memiliki akun?

                <a href="#">
                    Daftar sekarang
                </a>

            </div>


            {{-- BACK HOME --}}
            <a
                href="{{ url('/') }}"
                class="back-home"
            >

                <i class="bi bi-arrow-left"></i>

                Kembali ke Beranda

            </a>


        </div>

    </div>

</div>

</body>

</html>
