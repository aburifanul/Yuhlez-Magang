<!DOCTYPE html>

<html lang="id">

<head>


<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    @yield('title', 'Yuhlez Magang')
</title>


{{-- =====================================================
    VITE
====================================================== --}}

@vite([
    'resources/css/dashboard.css',
    'resources/js/dashboard.js'
])


{{-- =====================================================
    BOOTSTRAP ICONS
====================================================== --}}

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
>


{{-- =====================================================
    GOOGLE FONT
====================================================== --}}

<link
    rel="preconnect"
    href="https://fonts.googleapis.com"
>

<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" 
    rel="stylesheet" 
> 
 
 
@stack('styles') 
 
 
</head> 
 
<body> 
 
{{-- ========================================================= 
NAVBAR 
========================================================= --}} 
 
<header class="dashboard-navbar"> 
 
 
<div class="dashboard-navbar-container"> 
 
 
    {{-- LOGO --}} 
    <a 
        href="{{ route('dashboard.index') }}" 
        class="dashboard-logo" 
    > 
 
        <div class="dashboard-logo-icon"> 
            Y 
        </div> 
 
        <div class="dashboard-logo-text"> 
 
            <strong> 
                YUHLEZ 
            </strong> 
 
            <span> 
                MAGANG 
            </span> 
 
        </div> 
 
    </a> 
 
 
    {{-- DESKTOP NAVIGATION --}} 
    <nav class="dashboard-nav"> 
 
        <a 
            href="{{ route('dashboard.index') }}" 
            class="active" 
        > 
            Beranda 
        </a> 
 
        <a href="#"> 
            Magang 
        </a> 
 
        <a href="#"> 
            Perusahaan 
        </a> 
 
        <a href="#"> 
            Tentang Kami 
        </a> 
 
    </nav> 
 
 
    {{-- DESKTOP AUTH --}} 
    <div class="dashboard-auth"> 
 
        <a 
            href="#" 
            class="dashboard-login" 
        > 
            Masuk 
        </a> 
 
        <a 
            href="#" 
            class="dashboard-register" 
        > 
            Daftar 
        </a> 
 
    </div> 
 
 
    {{-- MOBILE MENU BUTTON --}} 
    <button 
        type="button" 
        class="dashboard-menu-button" 
        id="dashboardMenuButton" 
        aria-label="Menu" 
    > 
 
        <i class="bi bi-list"></i> 
 
    </button> 
 
</div> 
 
 
</header> 
 
{{-- ========================================================= 
MOBILE NAVIGATION 
========================================================= --}} 
 
<div 
    class="dashboard-mobile-menu" 
    id="dashboardMobileMenu" 
> 
 
 
<a 
    href="{{ route('dashboard.index') }}" 
    class="active" 
> 
    <i class="bi bi-house"></i> 
    Beranda 
</a> 
 
 
<a href="#"> 
    <i class="bi bi-search"></i> 
    Cari Magang 
</a> 
 
 
<a href="#"> 
    <i class="bi bi-buildings"></i> 
    Perusahaan 
</a> 
 
 
<a href="#"> 
    <i class="bi bi-info-circle"></i> 
    Tentang Kami 
</a> 
 
 
<div class="dashboard-mobile-auth"> 
 
    <a href="#"> 
        Masuk 
    </a> 
 
    <a href="#"> 
        Daftar 
    </a> 
 
</div> 
 
 
</div> 
 
{{-- ========================================================= 
MAIN CONTENT 
========================================================= --}} 
 
<main> 
 
 
@yield('content') 
 
 
</main> 
 
{{-- ========================================================= 
FOOTER 
========================================================= --}} 
 
<footer class="dashboard-footer"> 
 
 
<div class="dashboard-footer-container"> 
 
 
    <div class="dashboard-footer-main"> 
 
 
        {{-- BRAND --}} 
        <div class="dashboard-footer-brand"> 
 
            <a 
                href="{{ route('dashboard.index') }}" 
                class="dashboard-logo footer-logo" 
            > 
 
                <div class="dashboard-logo-icon"> 
                    Y 
                </div> 
 
                <div class="dashboard-logo-text"> 
 
                    <strong> 
                        YUHLEZ 
                    </strong> 
 
                    <span> 
                        MAGANG 
                    </span> 
 
                </div> 
 
            </a> 
 
 
            <p> 
                Yuhlez Magang merupakan platform yang 
                membantu mahasiswa menemukan kesempatan 
                magang dan mengembangkan pengalaman 
                untuk mempersiapkan karier. 
            </p> 
 
 
            {{-- SOCIAL MEDIA --}} 
            <div class="dashboard-social"> 
 
                <a href="#" aria-label="Instagram"> 
                    <i class="bi bi-instagram"></i> 
                </a> 
 
                <a href="#" aria-label="LinkedIn"> 
                    <i class="bi bi-linkedin"></i> 
                </a> 
 
                <a href="#" aria-label="Facebook"> 
                    <i class="bi bi-facebook"></i> 
                </a> 
 
                <a href="#" aria-label="Twitter"> 
                    <i class="bi bi-twitter-x"></i> 
                </a> 
 
            </div> 
 
        </div> 
 
 
 
        {{-- YUHLEZ --}} 
        <div class="dashboard-footer-column"> 
 
            <h4> 
                Yuhlez 
            </h4> 
 
            <a href="#"> 
                Tentang Kami 
            </a> 
 
            <a href="#"> 
                Cara Kerja 
            </a> 
 
            <a href="#"> 
                Hubungi Kami 
            </a> 
 
            <a href="#"> 
                FAQ 
            </a> 
 
        </div> 
 
 
 
        {{-- MAGANG --}} 
        <div class="dashboard-footer-column"> 
 
            <h4> 
                Magang 
            </h4> 
 
            <a href="#"> 
                Cari Magang 
            </a> 
 
            <a href="#"> 
                Lowongan Terbaru 
            </a> 
 
            <a href="#"> 
                Perusahaan 
            </a> 
 
            <a href="#"> 
                Kategori 
            </a> 
 
        </div> 
 
 
 
        {{-- BANTUAN --}} 
        <div class="dashboard-footer-column"> 
 
            <h4> 
                Bantuan 
            </h4> 
 
            <a href="#"> 
                Pusat Bantuan 
            </a> 
 
            <a href="#"> 
                Syarat & Ketentuan 
            </a> 
 
            <a href="#"> 
                Kebijakan Privasi 
            </a> 
 
        </div> 
 
    </div> 
 
 
 
    {{-- FOOTER BOTTOM --}} 
    <div class="dashboard-footer-bottom"> 
 
        <span> 
            © {{ date('Y') }} Yuhlez Magang. 
            All rights reserved. 
        </span> 
 
        <span> 
            Platform Magang Mahasiswa 
        </span> 
 
    </div> 
 
</div> 
 
 
</footer> 
 
@stack('scripts') 
 
</body> 
 
</html> 