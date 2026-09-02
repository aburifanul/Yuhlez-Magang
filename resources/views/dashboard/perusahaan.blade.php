@extends('dashboard.layouts.app')

@section('title', 'Perusahaan Mitra - Yuhlez Magang')

@section('content')

{{-- =========================================================
    HERO
========================================================= --}}

<section class="perusahaan-hero">

    <div class="dashboard-container">

        <div class="perusahaan-hero-content">

            <span class="perusahaan-eyebrow">
                <i class="bi bi-buildings"></i>
                PERUSAHAAN MITRA
            </span>

            <h1>
                Temukan
                <span>Perusahaan Impianmu</span>
            </h1>

            <p>
                Jelajahi berbagai perusahaan yang bekerja sama
                dengan Yuhlez dan temukan kesempatan magang
                yang sesuai dengan minat serta tujuan kariermu.
            </p>

        </div>

    </div>

</section>


{{-- =========================================================
    SEARCH & FILTER
========================================================= --}}

<section class="perusahaan-search-section">

    <div class="dashboard-container">

        <div class="perusahaan-search-card">

            {{-- SEARCH --}}

            <div class="perusahaan-search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchPerusahaan"
                    placeholder="Cari nama perusahaan atau bidang..."
                >

            </div>


            {{-- KATEGORI --}}

            <div class="perusahaan-filter">

                <select id="filterKategori">

                    <option value="">
                        Semua Kategori
                    </option>

                    <option value="Teknologi">
                        Teknologi
                    </option>

                    <option value="Desain & Kreatif">
                        Desain & Kreatif
                    </option>

                    <option value="Marketing">
                        Marketing
                    </option>

                    <option value="Bisnis">
                        Bisnis
                    </option>

                </select>

            </div>


            {{-- LOKASI --}}

            <div class="perusahaan-filter">

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


            {{-- BUTTON --}}

            <button
                type="button"
                class="perusahaan-search-button"
                id="searchButton"
            >

                <i class="bi bi-search"></i>

                Cari Perusahaan

            </button>

        </div>

    </div>

</section>


{{-- =========================================================
    PERUSAHAAN
========================================================= --}}

<section
    class="perusahaan-section"
    id="perusahaan"
>

    <div class="dashboard-container">


        {{-- HEADING --}}

        <div class="perusahaan-section-heading">

            <div>

                <span>
                    PERUSAHAAN MITRA
                </span>

                <h2>
                    Perusahaan yang Bekerja Sama
                </h2>

                <p>
                    Temukan perusahaan mitra yang menyediakan
                    berbagai kesempatan magang.
                </p>

            </div>


            <div class="perusahaan-result">

                <strong id="jumlahPerusahaan">
                    {{ count($perusahaan) }}
                </strong>

                <span>
                    perusahaan ditemukan
                </span>

            </div>

        </div>


        {{-- GRID --}}

        <div
            class="perusahaan-grid"
            id="perusahaanGrid"
        >

            @forelse ($perusahaan as $item)

                <div
                    class="perusahaan-card"
                    data-kategori="{{ $item['kategori'] }}"
                    data-lokasi="{{ $item['lokasi'] }}"
                >

                    {{-- CARD TOP --}}

                    <div class="perusahaan-card-top">

                        <div
                            class="perusahaan-logo {{ $item['warna'] }}"
                        >

                            <i class="{{ $item['icon'] }}"></i>

                        </div>


                        <button
                            type="button"
                            class="perusahaan-bookmark"
                            aria-label="Simpan perusahaan"
                        >

                            <i class="bi bi-bookmark"></i>

                        </button>

                    </div>


                    {{-- CATEGORY --}}

                    <span class="perusahaan-category">

                        {{ $item['kategori'] }}

                    </span>


                    {{-- COMPANY NAME --}}

                    <h3>

                        {{ $item['nama'] }}

                    </h3>


                    {{-- DESCRIPTION --}}

                    <p class="perusahaan-description">

                        {{ $item['deskripsi'] }}

                    </p>


                    {{-- INFO --}}

                    <div class="perusahaan-info">

                        <span>

                            <i class="bi bi-geo-alt"></i>

                            {{ $item['lokasi'] }}

                        </span>


                        <span>

                            <i class="bi bi-briefcase"></i>

                            {{ $item['program'] }} Program Magang

                        </span>

                    </div>


                    {{-- BOTTOM --}}

                    <div class="perusahaan-card-bottom">

                        <span class="perusahaan-status">

                            <i class="bi bi-circle-fill"></i>

                            Mitra Yuhlez

                        </span>


                        <a
                            href="#"
                            class="perusahaan-detail-button"
                        >

                            Lihat Perusahaan

                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>

                </div>

            @empty

                <div class="perusahaan-empty">

                    <div class="perusahaan-empty-icon">

                        <i class="bi bi-buildings"></i>

                    </div>

                    <h3>
                        Belum Ada Perusahaan
                    </h3>

                    <p>
                        Saat ini belum ada perusahaan yang tersedia.
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

<section class="perusahaan-cta-section">

    <div class="dashboard-container">

        <div class="perusahaan-cta">

            <div class="perusahaan-cta-content">

                <span>
                    TEMUKAN KESEMPATANMU
                </span>

                <h2>
                    Temukan perusahaan tempatmu berkembang.
                </h2>

                <p>
                    Jelajahi perusahaan mitra Yuhlez dan temukan
                    program magang yang sesuai dengan kemampuanmu.
                </p>

            </div>


            <a
                href="{{ route('magang.index') }}"
                class="perusahaan-cta-button"
            >

                Cari Program Magang

                <i class="bi bi-arrow-right"></i>

            </a>

        </div>

    </div>

</section>


{{-- =========================================================
    JAVASCRIPT
========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('searchPerusahaan');

    const kategori =
        document.getElementById('filterKategori');

    const lokasi =
        document.getElementById('filterLokasi');

    const searchButton =
        document.getElementById('searchButton');

    const cards =
        document.querySelectorAll('.perusahaan-card');

    const jumlahPerusahaan =
        document.getElementById('jumlahPerusahaan');


    function filterPerusahaan() {

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


        jumlahPerusahaan.textContent = jumlah;

    }


    searchButton.addEventListener(
        'click',
        filterPerusahaan
    );


    searchInput.addEventListener(
        'input',
        filterPerusahaan
    );


    kategori.addEventListener(
        'change',
        filterPerusahaan
    );


    lokasi.addEventListener(
        'change',
        filterPerusahaan
    );

});

</script>

@endpush

@endsection