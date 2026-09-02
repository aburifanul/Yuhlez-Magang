@extends('dashboard.layouts.app')

@section('title', 'Program Magang - Yuhlez Magang')

@section('content')

{{-- =========================================================
    HERO
========================================================= --}}

<section class="magang-hero">

    <div class="dashboard-container">

        <div class="magang-hero-content">

            <span class="magang-eyebrow">
                <i class="bi bi-briefcase"></i>
                PROGRAM MAGANG
            </span>

            <h1>
                Temukan Kesempatan
                <span>Magang Terbaikmu</span>
            </h1>

            <p>
                Jelajahi berbagai program magang dari perusahaan
                dan temukan kesempatan yang sesuai dengan minat,
                kemampuan, serta tujuan kariermu.
            </p>

        </div>

    </div>

</section>


{{-- =========================================================
    SEARCH & FILTER
========================================================= --}}

<section class="magang-search-section">

    <div class="dashboard-container">

        <div class="magang-search-card">

            <div class="magang-search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchMagang"
                    placeholder="Cari posisi, perusahaan, atau bidang..."
                >

            </div>


            <div class="magang-filter">

                <select id="filterKategori">

                    <option value="">
                        Semua Kategori
                    </option>

                    <option value="Teknologi">
                        Teknologi
                    </option>

                    <option value="Desain">
                        Desain
                    </option>

                    <option value="Marketing">
                        Marketing
                    </option>

                    <option value="Bisnis">
                        Bisnis
                    </option>

                </select>

            </div>


            <div class="magang-filter">

                <select id="filterLokasi">

                    <option value="">
                        Semua Lokasi
                    </option>

                    <option value="Jakarta">
                        Jakarta
                    </option>

                    <option value="Bandung">
                        Bandung
                    </option>

                    <option value="Surabaya">
                        Surabaya
                    </option>

                    <option value="Yogyakarta">
                        Yogyakarta
                    </option>

                </select>

            </div>


            <button
                type="button"
                class="magang-search-button"
                id="searchButton"
            >

                <i class="bi bi-search"></i>

                Cari Magang

            </button>

        </div>

    </div>

</section>


{{-- =========================================================
    PROGRAM MAGANG
========================================================= --}}

<section class="magang-program-section">

    <div class="dashboard-container">


        {{-- HEADER --}}

        <div class="magang-section-heading">

            <div>

                <span>
                    LOWONGAN TERSEDIA
                </span>

                <h2>
                    Program Magang
                </h2>

                <p>
                    Pilih program magang yang sesuai dengan
                    minat dan kemampuanmu.
                </p>

            </div>


            <div class="magang-result">

                <strong id="jumlahProgram">
                    {{ count($programMagang) }}
                </strong>

                <span>
                    program ditemukan
                </span>

            </div>

        </div>


        {{-- GRID --}}

        <div
            class="magang-grid"
            id="magangGrid"
        >


            @forelse ($programMagang as $program)

                <div
                    class="magang-card"
                    data-kategori="{{ $program['kategori'] }}"
                    data-lokasi="{{ $program['lokasi'] }}"
                >


                    {{-- CARD TOP --}}

                    <div class="magang-card-top">

                        <div class="magang-company-logo">

                            <i class="{{ $program['icon'] }}"></i>

                        </div>


                        <button
                            type="button"
                            class="magang-bookmark"
                            aria-label="Simpan program"
                        >

                            <i class="bi bi-bookmark"></i>

                        </button>

                    </div>


                    {{-- CATEGORY --}}

                    <span class="magang-category">

                        {{ $program['kategori'] }}

                    </span>


                    {{-- TITLE --}}

                    <h3>

                        {{ $program['judul'] }}

                    </h3>


                    {{-- COMPANY --}}

                    <p class="magang-company">

                        {{ $program['perusahaan'] }}

                    </p>


                    {{-- INFO --}}

                    <div class="magang-info">

                        <span>

                            <i class="bi bi-geo-alt"></i>

                            {{ $program['lokasi'] }}

                        </span>


                        <span>

                            <i class="bi bi-calendar3"></i>

                            {{ $program['durasi'] }}

                        </span>

                    </div>


                    {{-- BOTTOM --}}

                    <div class="magang-card-bottom">

                        <span class="magang-status">

                            <i class="bi bi-circle-fill"></i>

                            Pendaftaran dibuka

                        </span>


                        <a
                            href="#"
                            class="magang-detail-button"
                        >

                            Detail

                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>


                </div>

            @empty

                <div class="magang-empty">

                    <div class="magang-empty-icon">

                        <i class="bi bi-briefcase"></i>

                    </div>

                    <h3>
                        Belum Ada Program Magang
                    </h3>

                    <p>
                        Saat ini belum ada program magang yang tersedia.
                        Silakan cek kembali beberapa saat lagi.
                    </p>

                </div>

            @endforelse


        </div>

    </div>

</section>


{{-- =========================================================
    CTA
========================================================= --}}

<section class="magang-cta-section">

    <div class="dashboard-container">

        <div class="magang-cta">

            <div class="magang-cta-content">

                <span>
                    MULAI PERJALANANMU
                </span>

                <h2>
                    Siap menemukan magang impianmu?
                </h2>

                <p>
                    Jelajahi berbagai kesempatan magang dan
                    mulai bangun pengalaman profesionalmu
                    bersama Yuhlez.
                </p>

            </div>


            <a
                href="{{ route('dashboard.index') }}"
                class="magang-cta-button"
            >

                <i class="bi bi-arrow-left"></i>

                Kembali ke Beranda

            </a>

        </div>

    </div>

</section>


{{-- =========================================================
    SEARCH JAVASCRIPT
========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('searchMagang');

    const kategori =
        document.getElementById('filterKategori');

    const lokasi =
        document.getElementById('filterLokasi');

    const searchButton =
        document.getElementById('searchButton');

    const cards =
        document.querySelectorAll('.magang-card');

    const jumlahProgram =
        document.getElementById('jumlahProgram');


    function filterMagang() {

        const search =
            searchInput.value.toLowerCase().trim();

        const selectedKategori =
            kategori.value.toLowerCase();

        const selectedLokasi =
            lokasi.value.toLowerCase();


        let jumlah = 0;


        cards.forEach(function (card) {

            const text =
                card.innerText.toLowerCase();

            const cardKategori =
                card.dataset.kategori.toLowerCase();

            const cardLokasi =
                card.dataset.lokasi.toLowerCase();


            const cocokSearch =
                search === '' ||
                text.includes(search);


            const cocokKategori =
                selectedKategori === '' ||
                cardKategori === selectedKategori;


            const cocokLokasi =
                selectedLokasi === '' ||
                cardLokasi === selectedLokasi;


            if (
                cocokSearch &&
                cocokKategori &&
                cocokLokasi
            ) {

                card.style.display = '';

                jumlah++;

            } else {

                card.style.display = 'none';

            }

        });


        jumlahProgram.textContent = jumlah;

    }


    searchButton.addEventListener(
        'click',
        filterMagang
    );


    searchInput.addEventListener(
        'input',
        filterMagang
    );


    kategori.addEventListener(
        'change',
        filterMagang
    );


    lokasi.addEventListener(
        'change',
        filterMagang
    );

});

</script>

@endpush

@endsection