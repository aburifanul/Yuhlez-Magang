@extends('dashboard.layouts.app')

@section('title', 'Tentang Kami - Yuhlez Magang')

@push('styles')
    @vite('resources/css/tentang.css')
@endpush

@section('content')

{{-- =========================================================
    HERO
========================================================= --}}

<section class="tentang-hero">

    <div class="dashboard-container">

        <div class="tentang-hero-content">

            <span class="tentang-eyebrow">
                <i class="bi bi-info-circle"></i>
                TENTANG YUHLEZ
            </span>

            <h1>
                Menghubungkan Mahasiswa
                <span>dengan Dunia Profesional</span>
            </h1>

            <p>
                Yuhlez Magang hadir untuk membantu mahasiswa menemukan
                kesempatan magang yang sesuai sekaligus memberikan
                pengalaman profesional sebagai bekal menuju dunia kerja.
            </p>

        </div>

    </div>

</section>


{{-- =========================================================
    INTRODUCTION
========================================================= --}}

<section class="tentang-intro">

    <div class="dashboard-container">

        <div class="tentang-intro-grid">

            {{-- VISUAL --}}

            <div class="tentang-intro-visual">

                <div class="tentang-main-card">

                    <div class="tentang-card-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>

                    <strong>
                        YUHLEZ
                    </strong>

                    <span>
                        MAGANG
                    </span>

                </div>


                <div class="tentang-floating-card tentang-floating-one">

                    <i class="bi bi-search"></i>

                    <div>
                        <strong>Temukan</strong>
                        <span>Program Magang</span>
                    </div>

                </div>


                <div class="tentang-floating-card tentang-floating-two">

                    <i class="bi bi-building"></i>

                    <div>
                        <strong>Terhubung</strong>
                        <span>Perusahaan Mitra</span>
                    </div>

                </div>


                <div class="tentang-floating-card tentang-floating-three">

                    <i class="bi bi-lightbulb"></i>

                    <div>
                        <strong>Berkembang</strong>
                        <span>Bangun Pengalaman</span>
                    </div>

                </div>

            </div>


            {{-- CONTENT --}}

            <div class="tentang-intro-content">

                <span class="tentang-label">
                    SIAPA KAMI?
                </span>

                <h2>
                    Platform Magang
                    untuk Masa Depanmu
                </h2>

                <p>
                    Yuhlez Magang merupakan platform yang dirancang
                    untuk membantu mahasiswa menemukan berbagai
                    kesempatan magang dari perusahaan dan organisasi
                    yang tersedia.
                </p>

                <p>
                    Kami percaya bahwa pengalaman langsung di dunia
                    profesional merupakan bagian penting dalam
                    mempersiapkan mahasiswa menghadapi dunia kerja.
                    Karena itu, Yuhlez hadir untuk mempertemukan
                    mahasiswa dengan perusahaan melalui proses yang
                    lebih mudah dan terarah.
                </p>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
    VISI MISI
========================================================= --}}

<section class="tentang-visi-misi">

    <div class="dashboard-container">

        <div class="tentang-section-heading">

            <span>
                VISI & MISI
            </span>

            <h2>
                Membangun Jembatan Menuju Dunia Profesional
            </h2>

            <p>
                Kami berkomitmen untuk menciptakan ekosistem magang
                yang memberikan manfaat bagi mahasiswa maupun
                perusahaan.
            </p>

        </div>


        <div class="tentang-visi-grid">

            {{-- VISI --}}

            <div class="tentang-visi-card">

                <div class="tentang-visi-icon">
                    <i class="bi bi-eye"></i>
                </div>

                <div>

                    <span>
                        VISI
                    </span>

                    <h3>
                        Menjadi Platform Magang
                        yang Terpercaya
                    </h3>

                    <p>
                        Menjadi platform yang membantu mahasiswa
                        memperoleh pengalaman profesional yang
                        relevan serta mempertemukan mereka dengan
                        perusahaan yang tepat.
                    </p>

                </div>

            </div>


            {{-- MISI --}}

            <div class="tentang-visi-card">

                <div class="tentang-visi-icon">
                    <i class="bi bi-bullseye"></i>
                </div>

                <div>

                    <span>
                        MISI
                    </span>

                    <h3>
                        Memberikan Akses
                        Kesempatan yang Lebih Luas
                    </h3>

                    <p>
                        Menyediakan informasi program magang yang
                        mudah ditemukan serta membantu mahasiswa
                        mengembangkan pengalaman dan kemampuan
                        profesional mereka.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
    KEUNGGULAN
========================================================= --}}

<section class="tentang-feature-section">

    <div class="dashboard-container">

        <div class="tentang-section-heading">

            <span>
                MENGAPA YUHLEZ?
            </span>

            <h2>
                Lebih dari Sekadar Platform Magang
            </h2>

            <p>
                Kami membantu mahasiswa dari menemukan kesempatan
                hingga mendapatkan pengalaman yang bermakna.
            </p>

        </div>


        <div class="tentang-feature-grid">


            {{-- FEATURE 1 --}}

            <div class="tentang-feature-card">

                <div class="tentang-feature-icon">
                    <i class="bi bi-search"></i>
                </div>

                <h3>
                    Mudah Menemukan
                </h3>

                <p>
                    Cari program magang berdasarkan posisi,
                    kategori, lokasi, dan bidang yang sesuai
                    dengan minatmu.
                </p>

            </div>


            {{-- FEATURE 2 --}}

            <div class="tentang-feature-card">

                <div class="tentang-feature-icon">
                    <i class="bi bi-buildings"></i>
                </div>

                <h3>
                    Perusahaan Beragam
                </h3>

                <p>
                    Temukan berbagai kesempatan dari perusahaan
                    dengan bidang dan lingkungan kerja yang
                    beragam.
                </p>

            </div>


            {{-- FEATURE 3 --}}

            <div class="tentang-feature-card">

                <div class="tentang-feature-icon">
                    <i class="bi bi-person-check"></i>
                </div>

                <h3>
                    Pengalaman Profesional
                </h3>

                <p>
                    Dapatkan kesempatan untuk menerapkan ilmu
                    dan mengembangkan kemampuan melalui
                    pengalaman nyata.
                </p>

            </div>


            {{-- FEATURE 4 --}}

            <div class="tentang-feature-card">

                <div class="tentang-feature-icon">
                    <i class="bi bi-bar-chart-line"></i>
                </div>

                <h3>
                    Kembangkan Potensi
                </h3>

                <p>
                    Bangun kemampuan, pengalaman, dan portofolio
                    yang dapat menjadi bekal dalam perjalanan
                    kariermu.
                </p>

            </div>


        </div>

    </div>

</section>


{{-- =========================================================
    CARA KERJA
========================================================= --}}

<section class="tentang-how-section">

    <div class="dashboard-container">

        <div class="tentang-section-heading">

            <span>
                CARA KERJA
            </span>

            <h2>
                Mulai Perjalananmu Bersama Yuhlez
            </h2>

            <p>
                Hanya dengan beberapa langkah, kamu sudah bisa
                mulai mencari kesempatan magang.
            </p>

        </div>


        <div class="tentang-step-grid">


            {{-- STEP 1 --}}

            <div class="tentang-step">

                <div class="tentang-step-number">
                    01
                </div>

                <div class="tentang-step-icon">
                    <i class="bi bi-search"></i>
                </div>

                <h3>
                    Cari Program
                </h3>

                <p>
                    Jelajahi berbagai program magang yang
                    tersedia di Yuhlez.
                </p>

            </div>


            {{-- STEP 2 --}}

            <div class="tentang-step">

                <div class="tentang-step-number">
                    02
                </div>

                <div class="tentang-step-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>

                <h3>
                    Pilih Program
                </h3>

                <p>
                    Pilih program yang sesuai dengan minat
                    dan kemampuanmu.
                </p>

            </div>


            {{-- STEP 3 --}}

            <div class="tentang-step">

                <div class="tentang-step-number">
                    03
                </div>

                <div class="tentang-step-icon">
                    <i class="bi bi-send"></i>
                </div>

                <h3>
                    Daftar
                </h3>

                <p>
                    Kirimkan pendaftaran dan ikuti proses
                    seleksi dari perusahaan.
                </p>

            </div>


            {{-- STEP 4 --}}

            <div class="tentang-step">

                <div class="tentang-step-number">
                    04
                </div>

                <div class="tentang-step-icon">
                    <i class="bi bi-trophy"></i>
                </div>

                <h3>
                    Berkembang
                </h3>

                <p>
                    Dapatkan pengalaman dan kembangkan
                    kemampuan profesionalmu.
                </p>

            </div>


        </div>

    </div>

</section>


{{-- =========================================================
    CTA
========================================================= --}}

<section class="tentang-cta-section">

    <div class="dashboard-container">

        <div class="tentang-cta">

            <div>

                <span>
                    SIAP MEMULAI?
                </span>

                <h2>
                    Temukan Kesempatan Magangmu
                </h2>

                <p>
                    Jelajahi berbagai program magang dan mulai
                    bangun pengalaman profesional bersama Yuhlez.
                </p>

            </div>


            <a
                href="{{ route('magang.index') }}"
                class="tentang-cta-button"
            >

                Cari Program Magang

                <i class="bi bi-arrow-right"></i>

            </a>

        </div>

    </div>

</section>

@endsection