@extends('dashboard.layouts.app')

@section('title', 'Yuhlez Magang - Temukan Kesempatan Magang Terbaik')

@section('content')

{{-- =========================================================
    HERO SECTION
========================================================= --}}

<section class="dashboard-hero" id="beranda">

    <div class="dashboard-container">

        <div class="dashboard-hero-content">

            <div class="dashboard-hero-badge">
                <i class="bi bi-stars"></i>
                Platform Magang untuk Mahasiswa
            </div>

            <h1>
                FROM USELESS
                <span>TO YUHLEZ</span>
            </h1>

            <p>
                Temukan berbagai program magang dari perusahaan
                terbaik dan mulai bangun pengalaman profesionalmu
                bersama Yuhlez.
            </p>

            <div class="dashboard-hero-buttons">

                {{-- KE PROGRAM MAGANG --}}
                <a
                    href="{{ url('magang') }}#magang"
                    class="dashboard-btn-primary"
                >
                    <i class="bi bi-search"></i>
                    Cari Program Magang
                </a>

                {{-- KE PERUSAHAAN --}}
                <a
                    href="{{ url('perusahaan') }}#perusahaan"
                    class="dashboard-btn-primary"
                >
                    <i class="bi bi-search"></i>
                    Cari Perusahaan
                </a>

                {{-- KE TENTANG YUHLEZ --}}
                <a
                    href="{{ url('/tentang-kami') }}#tentang-yuhlez"
                    class="dashboard-btn-secondary"
                >
                    Pelajari Lebih Lanjut
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>


            <div class="dashboard-hero-stat">

                <div>
                    <strong>100+</strong>
                    <span>Program Magang</span>
                </div>

                <div>
                    <strong>50+</strong>
                    <span>Perusahaan</span>
                </div>

                <div>
                    <strong>500+</strong>
                    <span>Intern</span>
                </div>

            </div>

        </div>


        {{-- =====================================================
            HERO VISUAL
        ====================================================== --}}

        <div class="dashboard-hero-visual">

            <div class="hero-main-card">

                <div class="hero-card-top">

                    <div class="hero-card-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>

                    <div>

                        <span>
                            Program Terbaru
                        </span>

                        <strong>
                            Magang tersedia
                        </strong>

                    </div>

                    <i class="bi bi-three-dots"></i>

                </div>


                {{-- JOB 1 --}}

                <div class="hero-job-card">

                    <div class="hero-job-logo">
                        <i class="bi bi-code-slash"></i>
                    </div>

                    <div class="hero-job-info">

                        <strong>
                            Frontend Developer
                        </strong>

                        <span>
                            Yuhlez Technology
                        </span>

                        <small>
                            <i class="bi bi-geo-alt"></i>
                            Jakarta · Hybrid
                        </small>

                    </div>

                    <span class="hero-job-status">
                        Baru
                    </span>

                </div>


                {{-- JOB 2 --}}

                <div class="hero-job-card">

                    <div class="hero-job-logo purple">
                        <i class="bi bi-palette"></i>
                    </div>

                    <div class="hero-job-info">

                        <strong>
                            UI/UX Designer
                        </strong>

                        <span>
                            Creative Studio
                        </span>

                        <small>
                            <i class="bi bi-geo-alt"></i>
                            Bandung · Remote
                        </small>

                    </div>

                    <span class="hero-job-status">
                        Baru
                    </span>

                </div>


                {{-- JOB 3 --}}

                <div class="hero-job-card">

                    <div class="hero-job-logo green">
                        <i class="bi bi-megaphone"></i>
                    </div>

                    <div class="hero-job-info">

                        <strong>
                            Digital Marketing
                        </strong>

                        <span>
                            Nusantara Digital
                        </span>

                        <small>
                            <i class="bi bi-geo-alt"></i>
                            Surabaya · On-site
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    PROGRAM MAGANG
========================================================= --}}

<section
    class="dashboard-jobs-section"
    id="program-magang"
>

    <div class="dashboard-container">

        <div class="dashboard-section-heading">

            <div>

                <span>
                    PROGRAM MAGANG
                </span>

                <h2>
                    Temukan Program Magang
                </h2>

                <p>
                    Pilih program magang yang sesuai dengan minat
                    dan kemampuanmu.
                </p>

            </div>

            <a href="#program-magang">

                Lihat Semua

                <i class="bi bi-arrow-right"></i>

            </a>

        </div>


        <div class="dashboard-jobs-grid">


            {{-- =================================================
                PROGRAM 1
            ================================================== --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo">
                        <i class="bi bi-code-slash"></i>
                    </div>

                    <span class="job-bookmark">
                        <i class="bi bi-bookmark"></i>
                    </span>

                </div>

                <span class="job-label">
                    Teknologi
                </span>

                <h3>
                    Frontend Developer Intern
                </h3>

                <p class="job-company">
                    Yuhlez Technology
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-geo-alt"></i>
                        Jakarta
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        3 Bulan
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Pendaftaran dibuka
                    </span>

                    <a href="#">
                        Detail
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>



            {{-- =================================================
                PROGRAM 2
            ================================================== --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo purple">
                        <i class="bi bi-palette"></i>
                    </div>

                    <span class="job-bookmark">
                        <i class="bi bi-bookmark"></i>
                    </span>

                </div>

                <span class="job-label">
                    Desain
                </span>

                <h3>
                    UI/UX Designer Intern
                </h3>

                <p class="job-company">
                    Creative Digital Studio
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-geo-alt"></i>
                        Bandung
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        3 Bulan
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Pendaftaran dibuka
                    </span>

                    <a href="#">
                        Detail
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>



            {{-- =================================================
                PROGRAM 3
            ================================================== --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo orange">
                        <i class="bi bi-megaphone"></i>
                    </div>

                    <span class="job-bookmark">
                        <i class="bi bi-bookmark"></i>
                    </span>

                </div>

                <span class="job-label">
                    Marketing
                </span>

                <h3>
                    Digital Marketing Intern
                </h3>

                <p class="job-company">
                    Nusantara Digital
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-geo-alt"></i>
                        Surabaya
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        6 Bulan
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Pendaftaran dibuka
                    </span>

                    <a href="#">
                        Detail
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>



            {{-- =================================================
                PROGRAM 4
            ================================================== --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo green">
                        <i class="bi bi-bar-chart"></i>
                    </div>

                    <span class="job-bookmark">
                        <i class="bi bi-bookmark"></i>
                    </span>

                </div>

                <span class="job-label">
                    Bisnis
                </span>

                <h3>
                    Business Development Intern
                </h3>

                <p class="job-company">
                    Startup Indonesia
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-geo-alt"></i>
                        Yogyakarta
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        3 Bulan
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Pendaftaran dibuka
                    </span>

                    <a href="#">
                        Detail
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    PERUSAHAAN MITRA
========================================================= --}}

<section
    class="dashboard-category-section"
    id="perusahaan"
>

    <div class="dashboard-container">

        <div class="dashboard-section-heading">

            <div>

                <span>
                    PERUSAHAAN MITRA
                </span>

                <h2>
                    Perusahaan yang Bekerja Sama
                </h2>

                <p>
                    Berbagai perusahaan yang membuka kesempatan
                    magang melalui Yuhlez.
                </p>

            </div>

            <a href="#perusahaan">

                Lihat Semua

                <i class="bi bi-arrow-right"></i>

            </a>

        </div>


        <div class="dashboard-category-grid">


            {{-- COMPANY 1 --}}

            <a href="#" class="dashboard-category-card">

                <div class="category-icon blue">
                    <i class="bi bi-building"></i>
                </div>

                <div>

                    <h3>
                        Yuhlez Technology
                    </h3>

                    <p>
                        8 Program Magang
                    </p>

                </div>

                <i class="bi bi-arrow-up-right"></i>

            </a>



            {{-- COMPANY 2 --}}

            <a href="#" class="dashboard-category-card">

                <div class="category-icon purple">
                    <i class="bi bi-palette"></i>
                </div>

                <div>

                    <h3>
                        Creative Digital
                    </h3>

                    <p>
                        5 Program Magang
                    </p>

                </div>

                <i class="bi bi-arrow-up-right"></i>

            </a>



            {{-- COMPANY 3 --}}

            <a href="#" class="dashboard-category-card">

                <div class="category-icon orange">
                    <i class="bi bi-megaphone"></i>
                </div>

                <div>

                    <h3>
                        Nusantara Digital
                    </h3>

                    <p>
                        6 Program Magang
                    </p>

                </div>

                <i class="bi bi-arrow-up-right"></i>

            </a>



            {{-- COMPANY 4 --}}

            <a href="#" class="dashboard-category-card">

                <div class="category-icon green">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>

                <div>

                    <h3>
                        Startup Indonesia
                    </h3>

                    <p>
                        4 Program Magang
                    </p>

                </div>

                <i class="bi bi-arrow-up-right"></i>

            </a>



            {{-- COMPANY 5 --}}

            <a href="#" class="dashboard-category-card">

                <div class="category-icon red">
                    <i class="bi bi-shop"></i>
                </div>

                <div>

                    <h3>
                        Digital Kreatif
                    </h3>

                    <p>
                        7 Program Magang
                    </p>

                </div>

                <i class="bi bi-arrow-up-right"></i>

            </a>



            {{-- COMPANY 6 --}}

            <a href="#" class="dashboard-category-card">

                <div class="category-icon cyan">
                    <i class="bi bi-laptop"></i>
                </div>

                <div>

                    <h3>
                        Tech Nusantara
                    </h3>

                    <p>
                        5 Program Magang
                    </p>

                </div>

                <i class="bi bi-arrow-up-right"></i>

            </a>

        </div>

    </div>

</section>



{{-- =========================================================
    KARYA & PROJECT
========================================================= --}}

<section
    class="dashboard-jobs-section"
    id="karya"
>

    <div class="dashboard-container">

        <div class="dashboard-section-heading">

            <div>

                <span>
                    KARYA & PROJECT
                </span>

                <h2>
                    Karya yang Telah Diciptakan
                </h2>

                <p>
                    Lihat berbagai kegiatan dan project yang telah
                    dikerjakan oleh perusahaan bersama para intern.
                </p>

            </div>

            <a href="#karya">

                Lihat Semua

                <i class="bi bi-arrow-right"></i>

            </a>

        </div>


        <div class="dashboard-jobs-grid">


            {{-- PROJECT 1 --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo">
                        <i class="bi bi-window"></i>
                    </div>

                </div>

                <span class="job-label">
                    Web Development
                </span>

                <h3>
                    Website Company Profile
                </h3>

                <p class="job-company">
                    Yuhlez Technology
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-people"></i>
                        5 Intern
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        2026
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Project Magang
                    </span>

                    <a href="#">
                        Lihat Karya
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>



            {{-- PROJECT 2 --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo purple">
                        <i class="bi bi-phone"></i>
                    </div>

                </div>

                <span class="job-label">
                    UI/UX Design
                </span>

                <h3>
                    Aplikasi Manajemen Magang
                </h3>

                <p class="job-company">
                    Creative Digital
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-people"></i>
                        4 Intern
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        2026
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Project Magang
                    </span>

                    <a href="#">
                        Lihat Karya
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>



            {{-- PROJECT 3 --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo orange">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>

                </div>

                <span class="job-label">
                    Digital Marketing
                </span>

                <h3>
                    Kampanye Digital UMKM
                </h3>

                <p class="job-company">
                    Nusantara Digital
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-people"></i>
                        6 Intern
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        2026
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Project Magang
                    </span>

                    <a href="#">
                        Lihat Karya
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>



            {{-- PROJECT 4 --}}

            <div class="dashboard-job-card">

                <div class="job-card-top">

                    <div class="job-company-logo green">
                        <i class="bi bi-kanban"></i>
                    </div>

                </div>

                <span class="job-label">
                    Business
                </span>

                <h3>
                    Sistem Informasi Bisnis
                </h3>

                <p class="job-company">
                    Startup Indonesia
                </p>

                <div class="job-info">

                    <span>
                        <i class="bi bi-people"></i>
                        4 Intern
                    </span>

                    <span>
                        <i class="bi bi-calendar3"></i>
                        2026
                    </span>

                </div>

                <div class="job-card-bottom">

                    <span>
                        Project Magang
                    </span>

                    <a href="#">
                        Lihat Karya
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    TENTANG YUHLEZ
========================================================= --}}

<section
    class="dashboard-about-section"
    id="tentang-yuhlez"
>

    <div class="dashboard-container">

        <div class="dashboard-about-grid">


            {{-- =================================================
                VISUAL
            ================================================== --}}

            <div class="dashboard-about-visual">

                <div class="about-circle">

                    <div class="about-center-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>


                    <div class="about-mini-card card-one">

                        <i class="bi bi-search"></i>

                        <span>
                            Temukan
                        </span>

                    </div>


                    <div class="about-mini-card card-two">

                        <i class="bi bi-send"></i>

                        <span>
                            Daftar
                        </span>

                    </div>


                    <div class="about-mini-card card-three">

                        <i class="bi bi-trophy"></i>

                        <span>
                            Berkarya
                        </span>

                    </div>

                </div>

            </div>



            {{-- =================================================
                CONTENT
            ================================================== --}}

            <div class="dashboard-about-content">

                <span>
                    TENTANG YUHLEZ
                </span>

                <h2>
                    Menghubungkan Mahasiswa
                    dengan Dunia Profesional
                </h2>

                <p>
                    Yuhlez Magang merupakan platform yang membantu
                    mahasiswa menemukan kesempatan magang yang sesuai
                    dengan minat dan kemampuan mereka.
                </p>

                <p>
                    Melalui Yuhlez, mahasiswa dapat menemukan berbagai
                    program magang dari perusahaan mitra, mengikuti
                    kegiatan profesional, serta menghasilkan karya
                    bersama perusahaan.
                </p>


                <div class="dashboard-about-list">


                    {{-- FEATURE 1 --}}

                    <div>

                        <div class="about-list-icon">
                            <i class="bi bi-search"></i>
                        </div>

                        <div>

                            <strong>
                                Temukan Program
                            </strong>

                            <p>
                                Temukan berbagai program magang
                                yang sesuai dengan minatmu.
                            </p>

                        </div>

                    </div>



                    {{-- FEATURE 2 --}}

                    <div>

                        <div class="about-list-icon">
                            <i class="bi bi-building-check"></i>
                        </div>

                        <div>

                            <strong>
                                Perusahaan Terpercaya
                            </strong>

                            <p>
                                Berkolaborasi dengan berbagai
                                perusahaan dan organisasi.
                            </p>

                        </div>

                    </div>



                    {{-- FEATURE 3 --}}

                    <div>

                        <div class="about-list-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>

                        <div>

                            <strong>
                                Ciptakan Karya
                            </strong>

                            <p>
                                Dapatkan pengalaman dan hasilkan
                                karya selama mengikuti program.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    CTA
========================================================= --}}

<section class="dashboard-cta-section">

    <div class="dashboard-container">

        <div class="dashboard-cta">

            <div>

                <span>
                    SIAP MEMULAI?
                </span>

                <h2>
                    Temukan Program Magangmu
                </h2>

                <p>
                    Bergabung dengan Yuhlez dan mulai perjalananmu
                    menuju pengalaman profesional yang lebih baik.
                </p>

            </div>


            <a
                href="{{ url('/') }}#program-magang"
                class="dashboard-cta-button"
            >

                Lihat Program Magang

                <i class="bi bi-arrow-right"></i>

            </a>

        </div>

    </div>

</section>


@endsection