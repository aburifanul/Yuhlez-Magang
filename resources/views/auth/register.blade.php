@extends('dashboard.layouts.app')

@section('title', 'Daftar - Yuhlez Magang')

@push('styles')
    @vite('resources/css/auth.css')
@endpush

@push('scripts')
    @vite('resources/js/auth.js')
@endpush

@section('content')

<section class="auth-page">

    <div class="auth-container">

        <div class="auth-card">

            {{-- LOGO --}}
            <div class="auth-logo">

                <div class="auth-logo-icon">
                    Y
                </div>

                <div class="auth-logo-text">

                    <strong>
                        YUHLEZ
                    </strong>

                    <span>
                        MAGANG
                    </span>

                </div>

            </div>


            {{-- HEADING --}}
            <div class="auth-heading">

                <h1>
                    Buat Akun Baru
                </h1>

                <p>
                    Daftar dan mulai perjalanan magangmu
                    bersama Yuhlez.
                </p>

            </div>


            {{-- ERROR --}}
            @if ($errors->any())

                <div class="auth-alert">

                    <i class="bi bi-exclamation-circle"></i>

                    <div>

                        @foreach ($errors->all() as $error)

                            <div>
                                {{ $error }}
                            </div>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- GOOGLE --}}
            <a
                href="{{ route('google.redirect', [
                    'redirect' => request('redirect')
                ]) }}"
                class="auth-google-button"
            >

                <i class="bi bi-google"></i>

                <span>
                    Daftar dengan Google
                </span>

            </a>


            {{-- DIVIDER --}}
            <div class="auth-divider">

                <span>
                    atau daftar dengan email
                </span>

            </div>


            {{-- REGISTER FORM --}}
            <form
                action="{{ route('register.store') }}"
                method="POST"
            >

                @csrf

                <input
                    type="hidden"
                    name="redirect"
                    value="{{ request('redirect') }}"
                >


                {{-- NAMA --}}
                <div class="auth-form-group">

                    <label for="name">
                        Nama Lengkap
                    </label>

                    <div class="auth-input-wrapper">

                        <i class="bi bi-person"></i>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap"
                            autocomplete="name"
                            required
                        >

                    </div>

                </div>


                {{-- EMAIL --}}
                <div class="auth-form-group">

                    <label for="email">
                        Email
                    </label>

                    <div class="auth-input-wrapper">

                        <i class="bi bi-envelope"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Masukkan email"
                            autocomplete="email"
                            required
                        >

                    </div>

                </div>


                {{-- PASSWORD --}}
                <div class="auth-form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="auth-input-wrapper">

                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="auth-password-toggle"
                            id="togglePassword"
                            aria-label="Tampilkan password"
                        >

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </div>


                {{-- CONFIRM PASSWORD --}}
                <div class="auth-form-group">

                    <label for="password_confirmation">
                        Konfirmasi Password
                    </label>

                    <div class="auth-input-wrapper">

                        <i class="bi bi-lock-fill"></i>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="auth-password-toggle"
                            id="togglePasswordConfirmation"
                            aria-label="Tampilkan password"
                        >

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </div>


                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="auth-submit-button"
                >

                    <span>
                        Daftar
                    </span>

                    <i class="bi bi-arrow-right"></i>

                </button>

            </form>


            {{-- LOGIN --}}
            <div class="auth-footer-text">

                Sudah punya akun?

                <a
                    href="{{ route('login', [
                        'redirect' => request('redirect')
                    ]) }}"
                >
                    Masuk sekarang
                </a>

            </div>

        </div>

    </div>

</section>

@endsection